<?php

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
 * Error messages view helper
 *
 * = Examples =
 *
 * <code title="Output error messages as a list">
 * <ul class="errors">
 *   <f:form.errors>
 *     <li>{error.code}: {error.message}</li>
 *   </f:form.errors>
 * </ul>
 * </code>
 * <output>
 * <ul>
 *   <li>1234567890: Validation errors for argument "newBlog"</li>
 * </ul>
 * </output>
 *
 * <code title="Output error messages for a single property">
 * <f:form.errors for="someProperty">
 *   <div class="error">
 *     <strong>{error.propertyName}</strong>: <f:for each="{error.errors}" as="errorDetail">{errorDetail.message}</f:for>
 *   </div>
 * </f:form.errors>
 * </code>
 * <output>
 * <div class="error>
 *   <strong>someProperty:</strong> errorMessage1 errorMessage2
 * </div>
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class Tx_Ajaxlogin_ViewHelpers_FormErrorViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Iterates through selected errors of the request.
	 *
	 * @param string $object The name of the error name (e.g. argument name or property name). This can also be a property path (like blog.title), and will then only display the validation errors of that property.
	 * @param string $property
	 * @param string $as The name of the variable to store the current error
	 * @return string Rendered string
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @api
	 */
	public function render($object, $property, $as = 'error') {
		$propertyPath = explode('.', $object);
		foreach ($propertyPath as $currentPropertyName) {
			$errors = $this->getErrorsForProperty($currentPropertyName, $errors);
		}
		var_dump($propertyPath);
		$output = '';
		foreach ($errors as $errorKey => $error) {
			var_dump($error);
			$this->templateVariableContainer->add($as, $error);
			$output .= $this->renderChildren();
			$this->templateVariableContainer->remove($as);
		}
		return $output;
	}

	/**
	 * Find errors for a specific property in the given errors array
	 *
	 * @param string $propertyName The property name to look up
	 * @param array $errors An array of Tx_Fluid_Error_Error objects
	 * @return array An array of errors for $propertyName
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	protected function getErrorsForProperty($propertyName, $errors) {
		foreach ($errors as $error) {
			if ($error instanceof Tx_Extbase_Validation_PropertyError) {
				if ($error->getPropertyName() === $propertyName) {
					return $error->getErrors();
				}
			}
		}
		return array();
	}
}

?>