Event.observe(window, 'load', UserDashBoard_CommonSettings_init, false);

function UserDashBoard_CommonSettings_init()
{
    Event.observe('settings_AllowCustomizing_yes', 'click', settings_AllowCustomizing_onchange, false);
    Event.observe('settings_AllowCustomizing_no', 'click', settings_AllowCustomizing_onchange, false);

    if ( $('settings_AllowCustomizing_yes').checked) {
        $('settings_AllowCustomizing_container').hide();
    }
}


function settings_AllowCustomizing_onchange()
{
    radioswitchdisplaystate('settings_AllowCustomizing', 'settings_AllowCustomizing_container', false);
}
