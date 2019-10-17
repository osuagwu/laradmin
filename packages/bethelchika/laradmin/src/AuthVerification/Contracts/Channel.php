<?php

namespace BethelChika\Laradmin\AuthVerification\Contracts;

use Illuminate\Http\Request;
use BethelChika\Laradmin\AuthVerification\Models\AuthVerification;

interface Channel
{
     /**
     * Get the unique tag for the channel.
     *
     * @return string
     */
    public function getTag();

    /**
     * Get the title for the channel.
     *
     * @return string
     */
    public function getTitle();

    /**
     * The discription of the channel
     *
     * @return string
     */
    public function getDescription();




}
