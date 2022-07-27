<?php
class Service_LeaveBalancesheet extends Service_Base {

    public function doCredit($userId,$amount,$description,$actionTakenBy)
    {
        $leave = new Models_LeaveBalancesheet();
        $leave->userId = $userId;
        $leave->amount = $amount;
        $leave->type = 'credit';
        $leave->description = $description;
        $leave->actionTakenBy = $actionTakenBy;

        $isCreated = $leave->create();
        if($isCreated)
        {
            return true;
        }
        else{
            $this->addError($leave->getErrors);
        }
        return false;
    }

    public function doDebit($userId,$amount,$description,$actionTakenBy)
    {
        $leave = new Models_LeaveBalancesheet();
        $leave->userId = $userId;
        $leave->amount = $amount;
        $leave->type = 'debit';
        $leave->description = $description;
        $leave->actionTakenBy = $actionTakenBy;

        $isCreated = $leave->create();
        if($isCreated)
        {
            return true;
        }
        else{
            $this->addError($leave->getErrors);
        }
        return false;
    }

}