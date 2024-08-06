<?php

namespace App\Service;

use App\Entity\MessageContact;
use App\Enum\MessageStateEnum;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{

    public function __construct(private EntityManagerInterface $manager) { }

    public function persistMessage(MessageContact $message, bool $isBug = false) : bool {
        try{
            $message->setSentAt(new \DateTimeImmutable());
            $message->setState(MessageStateEnum::NON_LU);
            $message->setIsBug($isBug);
            $this->manager->persist($message);
            $this->manager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}