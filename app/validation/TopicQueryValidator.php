<?php
namespace App\Validation;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\Between;

class TopicQueryValidator extends Validation
{
    public function initialize(): void
    {
        $this->add('category', new PresenceOf());
        $this->add('category', new InclusionIn(['domain' => ['news', 'tech', 'life']]));
        $this->add('page', new Digit());
        $this->add('per_page', new Digit());
        $this->add('per_page', new Between(['minimum' => 1, 'maximum' => 100]));
    }
}
