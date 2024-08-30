<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\UserFunc;

use JWeiland\KommOneJobs\Configuration\JobFilter;

class AddFlexFormDataUserFunc
{
    public function setTypeItems(array &$params): void
    {
        foreach (JobFilter::TYPES as $key => $value) {
            $params['items'][] = [
                $value,
                $key
            ];
        }
    }

    public function setChannelItems(array &$params): void
    {
        foreach (JobFilter::CHANNELS as $key => $value) {
            $params['items'][] = [
                $value,
                $key
            ];
        }
    }
}
