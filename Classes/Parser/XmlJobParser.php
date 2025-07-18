<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Parser;

use JWeiland\KommOneJobs\Configuration\JobFilter;

class XmlJobParser
{
    public function parse(string $xmlContent, JobFilter $filter): array
    {
        $jobs = $this->xmlContent2Array($xmlContent);

        return array_filter($jobs, static function ($job) use ($filter): bool {
            foreach ($filter->getFilters() as $key => $filters) {
                if (in_array('all', $filters, true)) {
                    return true;
                }
                if (!in_array(strtolower($job[$key]), $filters, true)) {
                    return false;
                }
            }
            return true;
        });
    }

    private function xmlContent2Array(string $xmlContent): array
    {
        $structure = json_decode(json_encode((array)simplexml_load_string($xmlContent)), 1);
        if (!array_key_exists('publication', $structure)) {
            return [];
        }

        return $structure['publication'];
    }
}
