<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;
//use App\Database\Entities\MailChimpList;

/**
 * @ORM\Entity()
 */
class MailChimpListMembers extends MailChimpEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $memberId;
	
	/**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="status", type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(name="language", type="string", nullable=true)
     *
     * @var string
     */
    private $language;

    /**
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     *
     * @var bool
     */
    private $vip;

    /**
     * @ORM\Column(name="location", type="array")
     *
     * @var array
     */
    private $location;
	
	/**
     * @ORM\Column(name="mail_chimp_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpId;
	
	/**
     * @ORM\ManyToOne(targetEntity="App\Database\Entities\MailChimp\MailChimpList", inversedBy="mailChimpListMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $memberList;
	
	/**
     * Get id.
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->memberId;
    }

    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|email',
            'email_type' => 'nullable|string',
            'status' => 'required|string',
            'language' => 'nullable|string',
            'vip' => 'nullable|boolean',
            'location' => 'array',
            'location.latitude' => 'nullable|number',
            'location.longitude' => 'nullable|number'
        ];
    }
	
	
	public function getMemberList(): ?MailChimpList
    {
        return $this->memberList;
    }

    public function setMemberList(?MailChimpList $memberList): self
    {
        $this->memberList = $memberList;
        return $this;
    }

	/**
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return MailChimpListMembers
     */
    public function setEmailAddress(string $emailAddress): MailChimpListMembers
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }
	
	/**
     * Get mailchimp id of the list.
     *
     * @return null|string
     */
    public function getMailChimpId(): ?string
    {
        return $this->mailChimpId;
    }
	
	/**
     * Set mailchimp id of the list.
     *
     * @param string $mailChimpId
     *
     * @return \App\Database\Entities\MailChimp\MailChimpListMembers
     */
    public function setMailChimpId(string $mailChimpId): MailChimpListMembers
    {
        $this->mailChimpId = $mailChimpId;

        return $this;
    }
	
	/**
     * Set emailType.
     *
     * @param string $emailType
     *
     * @return MailChimpListMembers
     */
    public function setEmailType(string $emailType): MailChimpListMembers
    {
        $this->emailType = $emailType;

        return $this;
    }
	
	/**
     * Set status.
     *
     * @param string $status
     *
     * @return MailChimpListMembers
     */
    public function setStatus(string $status): MailChimpListMembers
    {
        $this->status = $status;

        return $this;
    }
	
	/**
     * Set language.
     *
     * @param string $language
     *
     * @return MailChimpListMembers
     */
    public function setLanguage(string $language): MailChimpListMembers
    {
        $this->language = $language;

        return $this;
    }
	
    /**
     * Set vip.
     *
     * @param string $vip
     *
     * @return MailChimpListMembers
     */
    public function setVip(bool $vip): MailChimpListMembers
    {
        $this->vip = $vip;

        return $this;
    }
	
    /**
     * Set location.
     *
     * @param string $location
     *
     * @return MailChimpListMembers
     */
    public function setLocation(array $location): MailChimpListMembers
    {
        $this->location = $location;

        return $this;
    }
	
    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }
	
	
}
