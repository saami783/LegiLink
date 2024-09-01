<?php

namespace App\Strategy;

use App\Entity\Api;
use App\Entity\Setting;
use App\Strategy\Abstract\AbstractFileStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdateDocxFileStrategy extends AbstractFileStrategy
{

}