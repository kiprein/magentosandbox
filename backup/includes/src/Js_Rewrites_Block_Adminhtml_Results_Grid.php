<?php

class Js_Rewrites_Block_Adminhtml_Results_Grid extends VladimirPopov_WebForms_Block_Adminhtml_Results_Grid
{
	/**
	 * The webform reply area is super hard to read if there are a lot of fields.  Instead just send the user to a
	 * readonly edit page.  The readonly makes sure to mimic what they previously had.
	 *
	 * @param $row
	 *
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'webform_id' => $row->getWebformId()));
	}
}