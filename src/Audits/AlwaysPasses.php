<?php namespace Celestriode\Constructure\Audits;

use Celestriode\Constructure\Statistics\Statistics;
use Celestriode\Constructure\Reports\ReportsInterface;
use Celestriode\Constructure\StructureInterface;
use Celestriode\Constructure\InputInterface;
use Celestriode\Constructure\Reports\Message;

class AlwaysPasses extends AbstractAudit
{
    public function audit(InputInterface $input, StructureInterface $expected, ReportsInterface $reports, Statistics $statistics): bool
    {
        $reports->addReport(Message::debug($input->getContext(), 'Audit passed'));

        return true;
    }
}