<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpListMembers;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class ListsMembersController extends Controller
{
    /**
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * ListsMembers constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Mailchimp\Mailchimp $mailchimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailchimp)
    {
        parent::__construct($entityManager);

        $this->mailChimp = $mailchimp;
    }

	/**
     * Add MailChimp list Members.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request, string $listId): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

		if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        // Instantiate entity
        $listMembers = new MailChimpListMembers($request->all());
        // Validate entity
        $validator = $this->getValidationFactory()->make($listMembers->toMailChimpArray(), $listMembers->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Update list into database
			$listMembers->setMemberList($list);
			
            $response =  $this->mailChimp->post(\sprintf('lists/%s/members', $list->getMailChimpId()), $listMembers->toMailChimpArray());
			$listMembers->setMailChimpId($response->get('id'));
			$this->saveEntity($listMembers);
			$list->addMembers($listMembers);
			$this->saveEntity($list);
			
			
            // Update list into MailChimp
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($listMembers->toArray());
    }
	
	
	/**
     * Update MailChimp list Member.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $listId, string $memberId): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }
		
		$member = $this->entityManager->getRepository(MailChimpListMembers::class)->find($memberId);

        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found', $memberId)],
                404
            );
        }
		if (!$list->getMembers()->contains($member)) {
			return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found in MailChimpList[%s]', $memberId, $listId)],
                404
            );
		}
		
		// Update list properties
		$member->fill($request->all());
		
        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Update list into database
            $this->saveEntity($member);
            // Update list into MailChimp
            $response = $this->mailChimp->patch(\sprintf('lists/%s/members/%s', $list->getMailChimpId(),$member->getMailChimpId()), $member->toMailChimpArray());
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }
	
	
	/**
     * Remove MailChimp list Member.
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(string $listId, string $memberId): JsonResponse
    {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->entityManager->getRepository(MailChimpList::class)->find($listId);

        if ($list === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }
		
		$member = $this->entityManager->getRepository(MailChimpListMembers::class)->find($memberId);

        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found', $memberId)],
                404
            );
        }
		if (!$list->getMembers()->contains($member)) {
			return $this->errorResponse(
                ['message' => \sprintf('MailChimpListMember[%s] not found in MailChimpList[%s]', $memberId, $listId)],
                404
            );
		}

        try {
            // Remove list from MailChimp
            $response =  $this->mailChimp->delete(\sprintf('lists/%s/members/%s', $list->getMailChimpId(),$member->getMailChimpId()));
            // Remove list from database
            $list->removeMembers($member);
			$this->removeEntity($member);
			
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    }

}
