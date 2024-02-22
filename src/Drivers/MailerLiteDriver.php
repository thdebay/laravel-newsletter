<?php

namespace Spatie\Newsletter\Drivers;

use Spatie\Newsletter\Support\Lists;
use MailerLite\MailerLite;

class MailerLiteDriver implements Driver
{
    protected Lists $lists;

    protected MailerLite $mailerLite;

    public static function make(array $arguments, Lists $lists)
    {
        return new self(arguments: $arguments, lists: $lists);
    }

    public function __construct(array $arguments, Lists $lists)
    {
        $this->mailerLite = new MailerLite(
            options: [
                'api_key' => $arguments['api_key']
            ],
        );

        $this->lists = $lists;
    }

    public function getApi(): MailerLite
    {
        return $this->mailerLite;
    }

    public function subscribe(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): bool {
        $list = $this->lists->findByName($listName);

        $response = $this->mailerLite->subscribers->create([
            'email' => $email,
        ]);

        $this->mailerLite->groups->assignSubscriber(
            groupId: $list->getId(),
            subscriberId: $this->extractSubscriberIdFromResponse($response),
        );

        return true; // fixme handle errors
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ) {
        return $this->subscribe(email: $email, properties: $properties, listName: $listName, options: $options);
    }

    public function getMember(string $email, string $listName = '')
    {
        // fixme
    }

    public function unsubscribe(string $email, string $listName = '')
    {
        // fixme
    }

    public function delete(string $email, string $listName = '')
    {
        // fixme
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        // fixme
        return true;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        // fixme
        return true;
    }

    protected function extractSubscriberIdFromResponse(string $response): string {
        return json_decode($response)['data']['id'];
    }
}
