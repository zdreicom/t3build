<?php
/**
 * Created by PhpStorm.
 * User: poppinga
 * Date: 10.08.17
 * Time: 20:34
 */
namespace Z3\T3build\Domain\Model\GitLab;

class Stage
{
    /**
     * @var string
     */
    protected $name = '';

    public function getName(): string
    {
        return $this->name;
    }
}
