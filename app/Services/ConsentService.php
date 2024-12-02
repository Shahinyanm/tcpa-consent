<?php

namespace App\Services;


use App\DTO\ConsentData;
use App\Enums\ConsentStatus;
use App\Events\ConsentStatusUpdated;
use App\Exceptions\InvalidVerificationCodeException;
use App\Exceptions\TwilioServiceException;
use App\Models\Consent;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Repositories\Interfaces\ConsentRepositoryInterface;
use App\Repositories\Interfaces\SmsLogRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class ConsentService
{
    public function __construct(
        private ConsentRepositoryInterface $consentRepository,
        private CompanyRepositoryInterface $companyRepository,
        private SmsLogRepositoryInterface  $smsLogRepository,
        private TwilioService              $twilioService
    ) {}

    /**
     * @throws Throwable
     * @throws TwilioServiceException
     */
    public function createConsent(ConsentData $data): Consent
    {
        try {
            return DB::transaction(function () use ($data) {
                $company = $this->companyRepository->getCompanyByHash($data->companyHash);
                $consent = $this->consentRepository->create($data,$company->id);

                $this->smsLogRepository->create([
                    'consent_id' => $consent->id,
                    'message'    => 'Verification code sent',
                    'direction'  => 'outgoing',
                ]);
                $this->twilioService->sendVerificationCode($consent);

                return $consent;
            });
        } catch (\Throwable $e) {
            Log::error('Failed to create consent: ' . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }
    /**
     * @throws Throwable
     * @throws TwilioServiceException
     * @throws InvalidVerificationCodeException
     */
    public function verifyCode(Consent $consent, string $code): bool
    {
        try {
            return DB::transaction(function () use ($consent, $code) {
                if ($consent->verification_code !== $code) {
                    $this->smsLogRepository->create([
                        'consent_id' => $consent->id,
                        'message'    => 'Invalid verification code attempt',
                        'direction'  => 'incoming',
                    ]);

                    throw new InvalidVerificationCodeException('Invalid verification code');
                }

                $this->consentRepository->updateStatus($consent, ConsentStatus::VERIFIED);

                $this->smsLogRepository->create([
                    'consent_id' => $consent->id,
                    'message'    => 'Consent verified',
                    'direction'  => 'outgoing',
                ]);

                $this->twilioService->sendConsentRequest($consent);

                return true;
            });
        } catch(InvalidVerificationCodeException $e) {
            throw $e;
        } catch(Throwable $e) {
            Log::error('Failed to verify code: ' . $e->getMessage());
            throw $e;
        }
    }

    public function handleIncomingSms(string $phoneNumber, string $message): void
    {
        DB::transaction(function () use ($phoneNumber, $message) {
            try {
                $consent = $this->consentRepository->findByPhoneNumber($phoneNumber);

                if (!$consent) {
                    $this->smsLogRepository->create([
                        'consent_id' => null,
                        'message'    => "Received SMS from unknown number: {$phoneNumber}",
                        'direction'  => 'incoming',
                    ]);

                    return;
                }

                $status = strtolower($message) === 'yes' ? ConsentStatus::CONSENTED : ConsentStatus::DECLINED;

                $this->smsLogRepository->create([
                    'consent_id' => $consent->id,
                    'message'    => $message,
                    'direction'  => 'incoming',
                ]);

                $this->consentRepository->updateStatus($consent, $status);

                $this->smsLogRepository->create([
                    'consent_id' => $consent->id,
                    'message'    => "Consent status updated to {$status->value}",
                    'direction'  => 'outgoing',
                ]);

                event(new ConsentStatusUpdated($consent));
            } catch (\Throwable $e) {
                Log::error('Error processing SMS: ' . $e->getMessage(), ['phoneNumber' => $phoneNumber, 'message' => $message]);
                throw $e;
            }
        });
    }}
