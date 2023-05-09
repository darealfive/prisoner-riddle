<?php
const STRATEGY_RANDOM      = 'random';
const STRATEGY_INTELLIGENT = 'intelligent';

/**
 * Set the strategy to either one of these above or implement your own strategy to pick a box number
 */
const STRATEGY = STRATEGY_INTELLIGENT;

const NUMBER_OF_PRISONS_TO_TEST = 300;
/**
 * Number of boxes/prisoners for the prisoners to overcome
 *
 * @return int
 */
$numberOfPrisonersInPrison = function () {
    /*
     * You can set random number of prisoners for the current prison to test here like
     */
//    return rand(50, 200);
    /*
     * Each prison have 200 prisoners, so any of these "NUMBER_OF_PRISONS_TO_TEST" prisoners must find their own number
     */
    return 200;
};

$prisonersTryToEscape = static function (int $numberOfBoxes) {
    $maxTries   = $numberOfBoxes / 2;
    $boxNumbers = $prisonerNumbers = $prisoners = range(1, $numberOfBoxes);
    /** @var int[] $boxNumbers */
    shuffle($boxNumbers);
    /** @var int[] $prisonerNumbers */
    shuffle($prisonerNumbers);
    /** @var int[] $prisoners */
    shuffle($prisoners);

    $boxes = array_combine($boxNumbers, $prisonerNumbers);

    $pickNumbers =
        static function (array $boxes, int $prisoner, int $boxNumber, int $tries) use (&$pickNumbers, $maxTries) {

            /*
             * Pick a box
             */
            $prisonerNumber = $boxes[$boxNumber];
            /*
             * Remove the box of the list
             */
            unset($boxes[$boxNumber]);
            /*
             * Check if prisoner found its own number and escape
             */
            if ($prisonerNumber === $prisoner) {

                return true;
            }

            switch (STRATEGY) {
                case STRATEGY_INTELLIGENT:
                    /*
                     * This is the best strategy! (probability ~30%)
                     */
                    $pickNumber = $prisonerNumber;
                    break;
                case STRATEGY_RANDOM:
                    /*
                     * This is a bad strategy!
                     */
                    $pickNumber = array_rand($boxes, 1);
                    break;
                default:
                    throw new DomainException('Strategy "' . STRATEGY . '" is unknown, please implement it yourself!');
            }

            /*
             * Check if prisoner have tries left
             */
            if ($tries < $maxTries) {
                return $pickNumbers($boxes, $prisoner, $pickNumber, ++$tries);
            }

            /*
             * Prisoner failed to escaped - all prisoners gets executed!!!
             */

            return false;
        };

    $results = [
        'success' => 0,
        'failed'  => 0,
    ];
    foreach ($prisoners as $prisoner) {

        $success = $pickNumbers($boxes, $prisoner, $prisoner, 1);
        if ($success) {
            $results['success']++;
        } else {
            $results['failed']++;
        }
    }

    return $results;
};

/*
 * Keeps track of the prisons having all its prisoners escaped successfully
 */
$prisonersSuccessfullyEscaped = 0;
for ($i = 1; $i <= NUMBER_OF_PRISONS_TO_TEST; $i++) {


    $numberOfBoxes = $numberOfPrisonersInPrison();
    $results       = $prisonersTryToEscape($numberOfBoxes);
    /*
     * Check if all prisoners have found their number
     */
    if ($results['success'] === $numberOfBoxes) {
        $prisonersSuccessfullyEscaped++;
    }
}

/*
 * Output calculated probability
 */
var_dump($prisonersSuccessfullyEscaped / NUMBER_OF_PRISONS_TO_TEST * 100);