<?php
declare(strict_types=1);

namespace Scaleplan\Sms\DTO;

use Scaleplan\DTO\DTO;
use OpenApi\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SmscDTO
 */
class SmscDTO extends DTO
{
    /**
     * @var int
     *
     * @Assert\Type(type="int", groups={"type"})
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank()
     *
     * @SWG\Property(property="id", type="int", nullable=false)
     */
    protected $id;

    /**
     * @var int
     *
     * @Assert\Type(type="int", groups={"type"})
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank()
     *
     * @SWG\Property(property="id", type="int", nullable=false)
     */
    protected $cnt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) : void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCnt()
    {
        return $this->cnt;
    }

    /**
     * @param int $cnt
     */
    public function setCnt($cnt) : void
    {
        $this->cnt = $cnt;
    }
}
