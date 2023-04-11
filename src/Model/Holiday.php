<?php

    namespace tahicz\Model;

    use DateTimeInterface;

    class Holiday
    {
        public const CLOSE = 'close';
        public const OPEN = 'open';


        /**
         * @var string|null
         */
        private ?string $name = null;
        /**
         * @var DateTimeInterface|null
         */
        private ?DateTimeInterface $date = null;
        private bool               $isEaster;

        public function __construct(string $name, DateTimeInterface $date, bool $isEaster = false)
        {
            $this->name     = $name;
            $this->date     = $date;
            $this->isEaster = $isEaster;
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
    }
