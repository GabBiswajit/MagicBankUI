<?php

namespace AGTHARN\BankUI\form;

use AGTHARN\BankUI\Main;
use pocketmine\player\Player;
use AGTHARN\BankUI\bank\Banks;
use jojoe77777\FormAPI\CustomForm;
use davidglitch04\libEco\libEco;

class TransferForm
{
    public static function transferForm(Player $player): CustomForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $coinsAtBank = $playerSession->money;
        $bankName = $playerSession->bankProvider;

        $form = new CustomForm(function (Player $player, ?array $data = null) use ($playerSession) {
            if ($data === null) {
                return;
            }
            $transferTax = Banks::getBankData($playerSession->bankProvider)["transferTax"];

            $player->sendForm(self::confirmTransferForm($player, $data[1], (float) $data[2], $transferTax));
        });
        
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->addLabel("If the player is offline, please type their name in exact.\n\nCoins at Bank: §e$$coinsAtBank");
        $form->addInput("Enter the Name of the Player");
        $form->addInput("Enter amount to transfer", "100000");
        
        return $form;
    }

    public static function confirmTransferForm(Player $player, string $receiverName, float $amount, float $transferTax): CustomForm
    {
        $playerSession = Main::getInstance()->getSessionManager()->getSession($player);
        $coinsInHand = libEco::myMoney($player, static function(float $money) : void {
	    var_dump($money);
         });
        $bankName = $playerSession->bankProvider;

        $form = new CustomForm(function (Player $player, ?array $data = null) use ($amount, $receiverName, $playerSession) {
            if ($data === null) {
                return;
            }

            $playerSession->transferMoney($amount, $receiverName, $data[1]);
        });
        
        $form->setTitle("§6» §r§l" . $bankName . " §r§6«");
        $form->addLabel("Coins in Hand: §e$$coinsInHand\n§rTransfer Amount: §e$$amount\n§rTransfer Tax: §e$$transferTax\n\n§rAre you sure you want to transfer this amount?");
        $form->addToggle("Amount Include Taxes?", false);
        
        return $form;
    }
}
