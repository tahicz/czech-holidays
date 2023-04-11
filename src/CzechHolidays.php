<?php

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

        private ArrayCollection $holidaysList;

        /**
         * Act No. 245/2000 Coll., on public holidays, other holidays, significant days and days off.
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
            '12-24' => Holiday::SHOP_CLOSE,
            '12-25' => Holiday::SHOP_CLOSE,
            '12-26' => Holiday::SHOP_CLOSE,
        ];

        private const GOOD_FRIDAY   = 'Velký pátek';
        private const EASTER_MONDAY = 'Velikonoční pondělí';

        public function __construct()
        {
            $this->holidaysList = new ArrayCollection();
        }

        /**
         * @param DateTimeInterface $dateTime
         *
         * @return bool
         * @throws Exception
         */
        public function isHoliday(DateTimeInterface $dateTime): bool
        {
            if ($this->holidaysList->isEmpty()) {
                $this->initHolidays($this->holidaysList, null);
            }
            $current         = new DateTimeImmutable();
            $isStableHoliday = $this->holidaysList->containsKey($dateTime->format(self::INDEX_FORMAT));
            if ($isStableHoliday) {
                return true;
            }

            $year = $dateTime->format('Y');

            if ($year !== $current->format('Y')) {
                foreach ($this->getEasterDatetime($year) as $holiday) {
                    if ($holiday->getDate() == $dateTime) {
                        return true;
                    }
                }
            }
            return false;

        }

        /**
         * @param int|null $year
         *
         * @return ArrayCollection
         * @throws Exception
         */
        public function getHolidays(?int $year): ArrayCollection
        {
            $current = new DateTimeImmutable();
            if (empty($year)) {
                $year = $current->format('Y');
            }
            if ($this->holidaysList->isEmpty()) {
                $this->initHolidays($this->holidaysList, $year);
            }
            return $this->holidaysList;
        }

        /**
         * @param ArrayCollection $list
         * @param int|null        $year
         *
         * @return void
         * @throws Exception
         */
        private function initHolidays(ArrayCollection $list, ?int $year): void
        {
            if (empty($year)) {
                $year = (int)date('Y');
            }
            foreach (self::HOLIDAYS as $date => $name) {
                if($date === '12-24'){
                    $holiday = new Holiday(
                        $name,
                        new DateTimeImmutable($year . '-' . $date),
                        false,
                        key_exists($date, self::OPENING_ON_HOLIDAYS) ? self::OPENING_ON_HOLIDAYS[$date] : Holiday::SHOP_OPEN_MORNING
                    );
                } else {
                    $holiday = new Holiday(
                        $name,
                        new DateTimeImmutable($year . '-' . $date),
                        false,
                        key_exists($date, self::OPENING_ON_HOLIDAYS) ? self::OPENING_ON_HOLIDAYS[$date] : Holiday::SHOP_OPEN
                    );
                }
                $index   = $holiday->getDate()->format(self::INDEX_FORMAT);
                $list->set($index, $holiday);
            }
            foreach ($this->getEasterDatetime($year) as $holiday) {
                $index = $holiday->getDate()->format(self::INDEX_FORMAT);
                $list->set($index, $holiday);
            }
        }

        /**
         * @see https://www.php.net/manual/en/function.easter-date.php#refsect1-function.easter-date-notes
         *
         * @param int $year
         *
         * @return Holiday[]
         * @throws Exception
         */
        private function getEasterDatetime(int $year): array
        {
            $base = new DateTime($year . '-03-21');
            $days = easter_days($year);

            $easterDay  = $base->add(new DateInterval('P' . $days . 'D')); //Velikonoční neděle, půlnoc
            $goodFriday = DateTimeImmutable::createFromMutable(
                $easterDay->modify('previous friday')
            );

            return [
                new Holiday(self::GOOD_FRIDAY, $goodFriday, true),
                new Holiday(self::EASTER_MONDAY, $goodFriday->modify('next monday'), true, Holiday::SHOP_CLOSE)
            ];
        }
    }
