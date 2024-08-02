<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support;

use Symfony\Component\Mime\Address;

class AddressNormalizer
{
    /**
     * @return array<int, Address>
     */
    public function normalize(?string $adresses): array
    {
        if (empty($adresses)) {
            return [];
        }

        $adresses = trim($adresses);

        return collect(str_getcsv($adresses))
            ->map(fn (string $address) => Address::create($address))
            ->toArray();
    }
}
