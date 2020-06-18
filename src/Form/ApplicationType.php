<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 06/05/2020
 * Time: 01:57
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
	/**
	 * @param string $label
	 * @param array  $attr
	 * @param array  $options
	 *
	 * @return array
	 */
	protected function getConfiguration($label, $attr = [], $options = []): array {
		return array_merge([
			'label' => $label,
			'attr'  => $attr
		], $options);
	}
}