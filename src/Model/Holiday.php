<?php

    namespace tahicz\Model;

    use DateTimeInterface;

    class Holiday
    {
        public const SHOP_CLOSE        = 'close';
        public const SHOP_OPEN         = 'open';
        public const SHOP_OPEN_MORNING = 'open_morning';


        /**
         * @var string|null
         */
        private ?string $name = null;
        /**
         * @var DateTimeInterface|null
         */
        private ?DateTimeInterface $date = null;
        private bool               $isEaster;
        private string             $openingRule;
        private string             $note = '';

        public function __construct(string $name, DateTimeInterface $date, bool $isEaster = false, string $openingRule = self::OPEN)
        {
            $this->name        = $name;
            $this->date        = $date;
            $this->isEaster    = $isEaster;
            $this->openingRule = $openingRule;
        }

        /**
         * @return string|null
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * @return DateTimeInterface|null
         */
        public function getDate(): ?DateTimeInterface
        {
            return $this->date;
        }

        /**
         * @return bool
         */
        public function isEaster(): bool
        {
            return $this->isEaster;
        }

        /**
         * @param string $note
         *
         * @return void
         */
        public function addNote(string $note): void
        {
            $this->note = $note;
        }

        public function getNote(): string
        {
            return $this->note;
        }

        /**
         * @return string
         */
        public function getOpeningRule(): string
        {
            return $this->openingRule;
        }

        public function isShopsOpen(): bool
        {
            if ($this->getOpeningRule() === self::SHOP_OPEN) {
                return true;
            }

            return false;
        }

    }
