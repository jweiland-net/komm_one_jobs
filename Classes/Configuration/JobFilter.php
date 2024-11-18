<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Configuration;

class JobFilter
{
    public const TYPES = [
        'all' => 'Show all',
        'dvvbw_professional' => 'Professional',
        'dvvbw_apprentice' => 'Apprentice',
        'dvvbw_holiday_job' => 'Holiday Job',
        'dvvbw_top_position' => 'Manager',
        'dvvbw_unsolicited' => 'Unsolicited',
    ];

    public const CHANNELS = [
        'all' => 'Show all',
        'homepage' => 'Homepage',
        'intranet' => 'Intranet',
        'multiposting' => 'Multiposting',
    ];

    private string $type = 'all';

    private string $channel = 'all';

    private string $search;

    private string $timeModel;

    private string $occupationalGroup;

    public function __construct(
        string $channel,
        string $type,
        string $search = '',
        string $timeModel = '',
        string $occupationalGroup = ''
    ) {
        if (in_array($type, array_keys(self::TYPES), true)) {
            $this->type = $type;
        }

        if (in_array($channel, array_keys(self::CHANNELS), true)) {
            $this->channel = $channel;
        }

        $this->search = htmlspecialchars(trim($search));
        $this->timeModel = htmlspecialchars(trim($timeModel));
        $this->occupationalGroup = htmlspecialchars(trim($occupationalGroup));
    }

    public function getFilters(): array
    {
        return [
            'vacancy_type' => $this->type,
            'publication_channel' => $this->channel,
        ];
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getTimeModel(): string
    {
        return $this->timeModel;
    }

    public function getOccupationalGroup(): string
    {
        return $this->occupationalGroup;
    }
}
