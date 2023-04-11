<?php
declare(strict_types=1);

namespace tahicz\Model;

use DateTimeInterface;

class Holiday
{
    public const SHOP_CLOSE = 'close';
    public const SHOP_OPEN = 'open';
    public const SHOP_OPEN_MORNING = 'open_morning';

    private ?string $_name;
    private DateTimeInterface $_date;
    private bool $_isEaster;
    private string $_openingRule;
    private string $_note = '';

    /**
     * Holiday class
     *
     * @param string            $name        Name of holiday
     * @param DateTimeInterface $date        Date of holiday
     * @param bool              $isEaster    is Easter holiday
     * @param string            $openingRule Shops openning flag
     *                                       <ul>
     *                                       <li>Holiday::SHOP_CLOSE</li>
     *                                       <li>Holiday::SHOP_OPEN</li>
     *                                       <li>Holiday::SHOP_OPEN_MORNING</li>
     *                                       </ul>
     */
    public function __construct(
        string            $name,
        DateTimeInterface $date,
        bool              $isEaster = false,
        string            $openingRule = self::SHOP_OPEN
    ) {
        $this->_name = $name;
        $this->_date = $date;
        $this->_isEaster = $isEaster;
        $this->_openingRule = $openingRule;
    }

    /**
     * Get holiday name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->_name;
    }

    /**
     * Get date of this holiday
     *
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->_date;
    }

    /**
     * Is this Easter holiday?
     *
     * @return bool
     */
    public function isEaster(): bool
    {
        return $this->_isEaster;
    }

    /**
     * Add note to holiday
     *
     * @param string $note note
     *
     * @return void
     */
    public function addNote(string $note): void
    {
        $this->_note = $note;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote(): string
    {
        return $this->_note;
    }

    /**
     * Get information about shop opening rules
     *
     * @return string
     */
    public function getOpeningRule(): string
    {
        return $this->_openingRule;
    }

    /**
     * Get information if shops are open by this holiday
     *
     * @return bool
     */
    public function isShopsOpen(): bool
    {
        if ($this->getOpeningRule() === self::SHOP_OPEN) {
            return true;
        }

        return false;
    }

}
