<?php
namespace SAV\SavLibraryPlus\ViewHelpers\Form;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Creates a submit button.
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <f:submit value="Send Mail" />
 * </code>
 *
 * Output:
 * <input type="submit" />
 *
 * <code title="Dummy content for template preview">
 * <f:submit name="mySubmit" value="Send Mail"><button>dummy button</button></f:submit>
 * </code>
 *
  * Output:
 * <input type="submit" name="mySubmit" value="Send Mail" />
 *
 * @version $Id: SubmitViewHelper.php 3109 2009-08-31 17:22:46Z bwaidelich $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class ImageViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper {

	
	/**
	 * @var string
	 */
	protected $tagName = 'input';

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', TRUE);
		$this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this view helper', FALSE, 'f3-form-error');
		$this->registerTagAttribute('disabled', 'string', 'Specifies that the input element should be disabled when the page loads');
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Renders the submit button.
	 *
	 * @param string type type attribute
	 * @param string value Value of submit tag
	 * @return string
	 * @author Laurent Foulloy <yolf.typo3@oranage.fr>
	 * @api
	 */
	public function render($src) {

    $name = \SAV\SavLibraryPlus\Controller\AbstractController::getFormName() . '[' . $this->arguments['name'] . ']';
		$this->tag->addAttribute('type', 'image');
		$this->tag->addAttribute('src', $src);
		$this->tag->addAttribute('name', $name);

    $this->setErrorClassAttribute();

		return $this->tag->render();
	}
}

?>
