<?php
namespace App\Tasks;

use Phalcon\Cli\Task;

class CrawlTask extends Task
{
    public function topicsAction()
    {
        echo "Crawling topics..." . PHP_EOL;
        // fetch RSS and insert/update topics
    }
}
