<?php
class Mgw_Modalcart_Model_Checkout_Onepage extends Mage_Checkout_Model_Type_Onepage{

    public function saveExcellence($data){
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $this->getQuote()->setPoNumber($data['poNumber']);
        $this->getQuote()->setComments($data['comments']);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
 
        $this->getCheckout()
            ->setStepData('excellence', 'allow', true)
            ->setStepData('excellence', 'complete', true)
            ->setStepData('review', 'allow', true);
 
        return array();
    }

    /**
     * Save additional order notes.
     */
    public function setAdditionalNotes($notes){
        $this->getQuote()->setComments($notes);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
    }

    /**
     * Retreive additional order notes.
     */
    public function getAdditionalNotes(){
        return $this->getQuote()->getData('comments');
    }
}