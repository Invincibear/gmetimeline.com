<?php

namespace GME;

class Event
{
    public string $headline = '';
    public string $description = '';
    public string|array $source = [];
    public string $icon = '';
    public string|array $tags = [];
    public bool $hidden = false;
    public bool $highlighted = false;
    public float $open = 0.00;
    public float $low = 0.00;
    public float $high = 0.00;
    public float $close = 0.00;
    public int $volume = 0;

    public function __construct(
        string|array $headline = '',
        string $description = '',
        string|array $source = '',
        string $icon = 'fas fa-power-off',
        string|array $tags = [],
        bool $hidden = false,
        bool $highlighted = false,
        float $open = 0.00,
        float $low = 0.00,
        float $high = 0.00,
        float $close = 0.00,
        int $volume = 0
    )
    {
        return $this
            ->setHeadline($headline)
            ->setDescription($description)
            ->setSource($source)
            ->setIcon($icon)
            ->setTags($tags)
            ->setHidden($hidden)
            ->setHighlighted($highlighted)
            ->setOpen($open)
            ->setLow($low)
            ->setHigh($high)
            ->setClose($close)
            ->setVolume($volume);
    }


    /**
     * @param string $description
     * @return string
     * Search for r/SubReddit and u/Username in timeline event descriptions and turn them into links
     */
    public function linkReddit(string $description): string
    {
        // Find text inside double quotes and FAIL it and SKIP it https://stackoverflow.com/a/20767160/1707636
        // then search for r/SubReddit or u/Username and turn those into links
        // requires a lowercase u/ or r/ to link, uppercase will be ignored
        $pattern = '/"[^"]*"(*SKIP)(*FAIL)|(u|r)\/([a-zA-Z0-9_\-]+)/m'; // variable to satisfy PhpStorm
        $description = preg_replace(
            $pattern,
            '<a href="https://reddit.com/$1/$2" target="_blank">$1/$2</a>',
            $description
        );

        // This replaces reddit.com/u/username with reddit.com/user/username
        // in the href="" tag. saves user's from a 301 redirect from Reddit
        $pattern = '/\=\"https\:\/\/reddit\.com\/u\/([a-zA-Z0-9_\-]+)\"\>/m';
        $description = preg_replace(
            $pattern,
            '="https://reddit.com/user/$1">',
            $description
        );

        return $description;
    }


    /**
     * @return string
     */
    public function getHeadline(): string
    {
        return $this->headline;
    }

    /**
     * @param string $headline
     * @return Event
     */
    public function setHeadline(string $headline): Event
    {
        $this->headline = $headline;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Event
     */
    public function setDescription(string $description): Event
    {
        $this->description = $this->linkReddit($description);
        return $this;
    }

    /**
     * @return string|array
     */
    public function getSource(): string|array
    {
        return $this->source;
    }

    /**
     * @param string|array $source
     * @return Event
     */
    public function setSource(string|array $source): Event
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getIcon(): string|array
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return Event
     */
    public function setIcon(string $icon): Event
    {
        $this->icon = ($icon === 'fas fa-' || $icon === 'far fa-' || $icon === 'fab fa-') ? '' : $icon;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getTags(): string|array
    {
        return $this->tags;
    }

    /**
     * @param string|array $tags
     * @return Event
     */
    public function setTags(string|array $tags): Event
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return Event
     */
    public function setHidden(bool $hidden): Event
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHighlighted(): bool
    {
        return $this->highlighted;
    }

    /**
     * @param bool $highlighted
     * @return Event
     */
    public function setHighlighted(bool $highlighted): Event
    {
        $this->highlighted = $highlighted;
        return $this;
    }

    /**
     * @return float
     */
    public function getOpen(): float
    {
        return $this->open;
    }

    /**
     * @param float $open
     * @return Event
     */
    public function setOpen(float $open): Event
    {
        $this->open = $open;
        return $this;
    }

    /**
     * @return float
     */
    public function getLow(): float
    {
        return $this->low;
    }

    /**
     * @param float $low
     * @return Event
     */
    public function setLow(float $low): Event
    {
        $this->low = $low;
        return $this;
    }

    /**
     * @return float
     */
    public function getHigh(): float
    {
        return $this->high;
    }

    /**
     * @param float $high
     * @return Event
     */
    public function setHigh(float $high): Event
    {
        $this->high = $high;
        return $this;
    }

    /**
     * @return float
     */
    public function getClose(): float
    {
        return $this->close;
    }

    /**
     * @param float $close
     * @return Event
     */
    public function setClose(float $close): Event
    {
        $this->close = $close;
        return $this;
    }

    /**
     * @return int
     */
    public function getVolume(): int
    {
        return $this->volume;
    }

    /**
     * @param int $volume
     * @return Event
     */
    public function setVolume(int $volume): Event
    {
        $this->volume = $volume;
        return $this;
    }
}