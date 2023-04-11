<?php
declare(strict_types=1);

namespace tahicz;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use tahicz\Model\Holiday;

class CzechHolidays
{
    private const INDEX_FORMAT = 'n-j';

    /**
     * List of holidays
     *
     * @var ArrayCollection<string,Holiday>
     */
    private ArrayCollection $_holidaysList;

    /**
     * Act No. 245/2000 Coll., on public holidays, other holidays,
     * significant days and days off.
     */
    private const HOLIDAYS = [
        '01-01' => 'Nový rok',
        '05-01' => 'Svátek práce',
        '05-08' => 'Den vítězství',
        '06-05' => 'Den slovanských věrozvěstů Cyrila a Metoděje',
        '06-06' => 'Den upálení mistra Jana Husa',
        '09-28' => 'Den české státnosti',
        '10-28' => 'Den vzniku samostatného československého státu',
        '11-17' => 'Den boje za svobodu a demokracii',
        '12-24' => 'Štědrý den',
        '12-25' => '1. svátek vánoční',
        '12-26' => '2. svátek vánoční'
    ];

    private const OPENING_ON_HOLIDAYS = [
        '01-01' => Holiday::SHOP_CLOSE,
        '05-08' => Holiday::SHOP_CLOSE,
        '09-28' => Holiday::SHOP_CLOSE,
        '10-28' => Holiday::SHOP_CLOSE,
        '12-24' => Holiday::SHOP_OPEN_MORNING,
        '12-25' => Holiday::SHOP_CLOSE,
        '12-26' => Holiday::SHOP_CLOSE,
    ];

    private const GOOD_FRIDAY = 'Velký pátek';
    private const EASTER_MONDAY = 'Velikonoční pondělí';

    /**
     * Czech holiday init
     */
    public function __construct()
    {
        $this->_holidaysList = new ArrayCollection();
    }

    /**
     * Check if given date is holiday or not
     *
     * @param DateTimeInterface $date Date for holiday check
     *
     * @return bool
     * @throws Exception
     */
    public function isHoliday(DateTimeInterface $date): bool
    {
        if ($this->_holidaysList->isEmpty()) {
            $this->_initHolidays($this->_holidaysList, null);
        }
        $current = new DateTimeImmutable();
        $isStableHoliday = $this->_holidaysList->containsKey(
            $date->format(self::INDEX_FORMAT)
        );
        if ($isStableHoliday) {
            return true;
        }

        $year = (int)$date->format('Y');

        if ($year !== (int)$current->format('Y')) {
            foreach ($this->_getEasterDatetime($year) as $holiday) {
                if ($holiday->getDate() == $date) {
                    return true;
                }
            }
        }
        return false;

    }

    /**
     * Returns holidays list for given year
     *
     * @param int|null $year Year for holiday list
     *
     * @return ArrayCollection<string,Holiday>
     * @throws Exception
     */
    public function getHolidays(?int $year): ArrayCollection
    {
        $current = new DateTimeImmutable();
        if (empty($year)) {
            $year = (int)$current->format('Y');
        }
        if ($this->_holidaysList->isEmpty()) {
            $this->_initHolidays($this->_holidaysList, $year);
        }
        return $this->_holidaysList;
    }

    /**
     * Generates Czech holidays list
     *
     * @param ArrayCollection<string,Holiday> $list Holiday list
     * @param int|null                        $year Year of holidays
     *
     * @return void
     * @throws Exception
     */
    private function _initHolidays(ArrayCollection $list, ?int $year): void
    {
        if (empty($year)) {
            $year = (int)date('Y');
        }
        foreach (self::HOLIDAYS as $date => $name) {
            if (key_exists($date, self::OPENING_ON_HOLIDAYS)) {
                $openingRule = self::OPENING_ON_HOLIDAYS[$date];
            } else {
                $openingRule = Holiday::SHOP_OPEN;
            }
            $holiday = new Holiday(
                $name,
                new DateTimeImmutable($year . '-' . $date),
                false,
                $openingRule
            );
            $index = $holiday->getDate()->format(self::INDEX_FORMAT);
            $list->set($index, $holiday);
        }
        foreach ($this->_getEasterDatetime($year) as $holiday) {
            $index = $holiday->getDate()->format(self::INDEX_FORMAT);
            $list->set($index, $holiday);
        }
    }

    /**
     * Calculate Easter holidays for given year
     *
     * @param int $year Year of Easter
     *
     * @return Holiday[]
     * @throws Exception
     *
     * @see https://www.php.net/manual/en/function.easter-date.php#refsect1-function.easter-date-notes
     */
    private function _getEasterDatetime(int $year): array
    {
        $base = new DateTime($year . '-03-21');
        $days = easter_days($year);

        //Velikonoční neděle, půlnoc
        $easterDay = $base->add(new DateInterval('P' . $days . 'D'));
        $goodFriday = DateTimeImmutable::createFromMutable(
            $easterDay->modify('previous friday')
        );

        return [
            new Holiday(self::GOOD_FRIDAY, $goodFriday, true),
            new Holiday(
                self::EASTER_MONDAY,
                $goodFriday->modify('next monday'),
                true,
                Holiday::SHOP_CLOSE
            )
        ];
    }
}
