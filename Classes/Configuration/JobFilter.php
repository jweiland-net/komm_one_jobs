<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Configuration;

use TYPO3\CMS\Core\Utility\MathUtility;

class JobFilter
{
    public const TYPES = [
        'dvvbw_professional' => 'Professional',
        'dvvbw_apprentice' => 'Apprentice',
        'dvvbw_holiday_job' => 'Holiday Job'
    ];

    public const CHANNELS = [
        0 => 'Homepage',
        1 => 'Intranet',
    ];

    private string $type = 'dvvbw_professional';

    private int $channel;

    public function __construct(int $channel, string $type)
    {
        if (in_array($type, array_keys(self::TYPES), true)) {
            $this->type = $type;
        }
        $this->channel = MathUtility::forceIntegerInRange($channel, 0, 1);
    }

    public function getFilters(): array
    {
        return [
            'vacancy_type' => $this->type,
            'publication_channel' => self::CHANNELS[$this->channel],
        ];
    }
}
