<?php

{{namespace}}

use PML5Packages\QuoteEngine\BaseAgentsSeeder;

use PML5Packages\I18n\Models\Unit;
use PML5Packages\I18n\Models\Locale;
use PML5Packages\I18n\Models\Currency;
use PML5Packages\I18n\Models\Country;
use PML5Packages\QuoteEngine\Models\Agent;
use PML5Packages\QuoteEngine\Models\Carrier;
use PML5Packages\QuoteEngine\Models\Service;
use PML5Packages\QuoteEngine\Models\ServiceTranslation;
use PML5Packages\QuoteEngine\Models\ServiceRule;
use PML5Packages\QuoteEngine\Models\ServiceValidator;
use PML5Packages\QuoteEngine\Models\ServiceWeightModifier;
use PML5Packages\QuoteEngine\Models\ServiceWeightModification;
use PML5Packages\QuoteEngine\Models\ServiceTransitDuration;
use PML5Packages\QuoteEngine\Models\Offer;
use PML5Packages\Orders\Models\Address;
use PML5Packages\Orders\Models\ProtectionCoverBand;

class {{model}} extends BaseAgentsSeeder
{
	protected function getAgentData()
	{
		return [];
	}
	
	protected function getAgentAddressData()
	{
		return [];
	}

	public function getAgentServicesData(Agent $agent)
	{
		return [];
	}

	protected function getAgentProtectionCoverData()
	{
		return [];
	}
}