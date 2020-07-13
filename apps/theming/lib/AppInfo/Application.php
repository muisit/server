<?php
/**
 * @copyright Copyright (c) 2019 Robin Appelman <robin@icewind.nl>
 *
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Robin Appelman <robin@icewind.nl>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Theming\AppInfo;

use OCA\Theming\Capabilities;
use OCA\Theming\Listener\LoadAdditionalScriptsListener;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\EventDispatcher\Event;

class Application extends App implements IBootstrap {
	public const APP_ID = 'theming';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerCapability(Capabilities::class);
		$context->registerEventListener(TemplateResponse::EVENT_LOAD_ADDITIONAL_SCRIPTS, LoadAdditionalScriptsListener::class);
	}

	public function boot(IBootContext $context): void {
		// TODO migrate this to the new IEventDispatcher
		$container = $context->getAppContainer();
		$context->getServerContainer()->getEventDispatcher()->addListener('OCA\Files_Sharing::loadAdditionalScripts', function() use ($container) {
			$listener = $container->query(LoadAdditionalScriptsListener::class);
			$listener->handle(new Event());
		});
	}
}
