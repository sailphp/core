<?php
/**
 * This was rushed asf will clean up at a later date
 */

namespace SailPHP\Database;
use Illuminate\Support\HtmlString;

/**
 * Class Presenter
 * @package SailPHP\Database
 */
class Presenter
{

    /**
     * @var null
     */
    private $paginator = null;
    /**
     * @var array|null
     */
    private $window = null;

    /**
     * Presenter constructor.
     * @param $paginator
     */
    public function __construct($paginator)
    {
        $this->paginator = $paginator;
        $this->window = $this->get();
    }

    /**
     * @return array
     */
    protected function getSmallSlider()
    {
        return [
            'first'  => $this->paginator->getUrlRange(1, $this->lastPage()),
            'slider' => null,
            'last'   => null,
        ];
    }

    /**
     * @return HtmlString|string
     */
    public function links()
    {
        if ($this->paginator->hasPages()) {
            return new HtmlString(sprintf(
                '<ul class="pagination">%s %s %s</ul>',
                $this->getPreviousButton(),
                $this->getLinks(),
                $this->getNextButton()
            ));
        }

        return '';
    }

    /**
     * @param $url
     * @param $page
     * @param null $rel
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="'.$rel.'"';
        return '<li class="page-item"><a class="page-link" href="'.htmlentities($url).'"'.$rel.'>'.$page.'</a></li>';
    }

    /**
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * @return mixed
     */
    protected function currentPage()
    {
        return $this->paginator->currentPage();
    }

    /**
     * @return mixed
     */
    protected function lastPage() {
        return $this->paginator->lastPage();
    }

    /**
     * @param $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="active page-item"><a class="page-link">'.$text.'</a></li>';
    }

    /**
     * @param $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<li class="disabled page-item"><a class="page-link" aria-disabled="diabled">'.$text.'</a></li>';
    }

    /**
     * @param string $text
     * @return string
     */
    public function getPreviousButton($text = '&laquo;')
    {
        // If the current page is less than or equal to one, it means we can't go any
        // further back in the pages, so we will render a disabled previous button
        // when that is the case. Otherwise, we will give it an active "status".
        if ($this->paginator->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }
        $url = $this->paginator->url(
            $this->paginator->currentPage() - 1
        );
        return $this->getPageLinkWrapper($url, $text, 'prev');
    }

    /**
     * @param string $text
     * @return string
     */
    public function getNextButton($text = '&raquo;')
    {
        // If the current page is greater than or equal to the last page, it means we
        // can't go any further into the pages, as we're already on this last page
        // that is available, so we will make it the "next" link style disabled.
        if (! $this->paginator->hasMorePages()) {
            return $this->getDisabledTextWrapper($text);
        }
        $url = $this->paginator->url($this->paginator->currentPage() + 1);
        return $this->getPageLinkWrapper($url, $text, 'next');
    }

    /**
     * @param int $onEachSide
     * @return array
     */
    public function get($onEachSide = 3)
    {
        if ($this->paginator->lastPage() < ($onEachSide * 2) + 6) {
            return $this->getSmallSlider();
        }
        return $this->getUrlSlider($onEachSide);
    }

    /**
     * @param $onEachSide
     * @return array
     */
    protected function getUrlSlider($onEachSide)
    {
        $window = $onEachSide * 2;
        if(!$this->hasPages()) {
            return [
                'first' => null,
                'slider'    => null,
                'last'      => null
            ];
        }

        if($this->paginator->currentPage() <= $window) {
            return $this->getSliderTooCloseToBeginning($window);
        } elseif ($this->currentPage() > ($this->lastPage() - $window)) {
            return $this->getSliderTooCloseToEnding($window);
        }

        return $this->getFullSlider($onEachSide);
    }

    /**
     * @param $onEachSide
     * @return array
     */
    protected function getFullSlider($onEachSide)
    {
        return [
            'first'  => $this->getStart(),
            'slider' => $this->getAdjacentUrlRange($onEachSide),
            'last'   => $this->getFinish(),
        ];
    }

    /**
     * @param $onEachSide
     * @return mixed
     */
    public function getAdjacentUrlRange($onEachSide)
    {
        return $this->paginator->getUrlRange(
            $this->currentPage() - $onEachSide,
            $this->currentPage() + $onEachSide
        );
    }

    /**
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->lastPage() > 1;
    }

    /**
     * @param $window
     * @return array
     */
    protected function getSliderTooCloseToBeginning($window)
    {
        return [
            'first'  => $this->paginator->getUrlRange(1, $window + 2),
            'slider' => null,
            'last'   => $this->getFinish(),
        ];
    }

    /**
     * @param $window
     * @return array
     */
    protected function getSliderTooCloseToEnding($window)
    {
        $last = $this->paginator->getUrlRange(
            $this->lastPage() - ($window + 2),
            $this->lastPage()
        );
        return [
            'first'  => $this->getStart(),
            'slider' => null,
            'last'   => $last,
        ];
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->paginator->getUrlRange(1, 2);
    }

    /**
     * @return mixed
     */
    public function getFinish()
    {
        return $this->paginator->getUrlRange(
            $this->lastPage() - 1,
            $this->lastPage()
        );
    }

    /**
     * @return string
     */
    protected function getLinks()
    {
        $html = '';
        if (is_array($this->window['first'])) {
            $html .= $this->getUrlLinks($this->window['first']);
        }
        if (is_array($this->window['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($this->window['slider']);
        }
        if (is_array($this->window['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($this->window['last']);
        }
        return $html;
    }

    /**
     * @param $url
     * @param $page
     * @param null $rel
     * @return string
     */
    protected function getPageLinkWrapper($url, $page, $rel = null)
    {
        if ($page == $this->paginator->currentPage()) {
            return $this->getActivePageWrapper($page);
        }
        return $this->getAvailablePageWrapper($url, $page, $rel);
    }

    /**
     * @param array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';
        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }
        return $html;
    }
}
