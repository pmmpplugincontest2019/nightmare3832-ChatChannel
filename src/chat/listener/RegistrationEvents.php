<?php

namespace chat\listener;

interface RegistrationEvents{
    const REGISTRATION_EVENTS = [
        'player\\PlayerChatEvent',
        'player\\PlayerLoginEvent'
    ];
}
