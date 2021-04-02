<?php

/**
* Show dashboard page.
*/
function DisplayDashboardTft()
{

    $status = new StatusMessages();
    // Need this check interface name for proper shell execution.
    if (!preg_match('/^([a-zA-Z0-9]+)$/', RASPI_WIFI_CLIENT_INTERFACE)) {
        $status->addMessage(_('Interface name invalid.'), 'danger');
        $status->showMessages();
        return;
    }

    if (!function_exists('exec')) {
        $status->addMessage(_('Required exec function is disabled. Check if exec is not added to php disable_functions.'), 'danger');
        $status->showMessages();
        return;
    }

    exec('ip a show '.RASPI_WIFI_CLIENT_INTERFACE, $stdoutIp);
    $stdoutIpAllLinesGlued = implode(" ", $stdoutIp);
    $stdoutIpWRepeatedSpaces = preg_replace('/\s\s+/', ' ', $stdoutIpAllLinesGlued);

    preg_match('/link\/ether ([0-9a-f:]+)/i', $stdoutIpWRepeatedSpaces, $matchesMacAddr) || $matchesMacAddr[1] = _('No MAC Address Found');
    $macAddr = $matchesMacAddr[1];

    $ipv4Addrs = '';
    $ipv4Netmasks = '';
    if (!preg_match_all('/inet (\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\/([0-3][0-9])/i', $stdoutIpWRepeatedSpaces, $matchesIpv4AddrAndSubnet)) {
        $ipv4Addrs = _('No IPv4 Address Found');
    } else {
        $numMatchesIpv4AddrAndSubnet = count($matchesIpv4AddrAndSubnet);
        for ($i = 1; $i < $numMatchesIpv4AddrAndSubnet; $i += 2) {
            if ($i > 2) {
                $ipv4Netmasks .= ' ';
                $ipv4Addrs .= ' ';
            }

            $ipv4Addrs .= $matchesIpv4AddrAndSubnet[$i][0];
            $ipv4Netmasks .= long2ip(-1 << (32 -(int)$matchesIpv4AddrAndSubnet[$i+1][0]));
        }
    }

    $ipv6Addrs = '';
    if (!preg_match_all('/inet6 ([a-f0-9:]+)/i', $stdoutIpWRepeatedSpaces, $matchesIpv6Addr)) {
        $ipv6Addrs = _('No IPv6 Address Found');
    } else {
        $numMatchesIpv6Addr = count($matchesIpv6Addr);
        for ($i = 1; $i < $numMatchesIpv6Addr; ++$i) {
            if ($i > 1) {
                $ipv6Addrs .= ' ';
            }

            $ipv6Addrs .= $matchesIpv6Addr[$i];
        }
    }

    preg_match('/state (UP|DOWN)/i', $stdoutIpWRepeatedSpaces, $matchesState) || $matchesState[1] = 'unknown';
    $interfaceState = $matchesState[1];

    // Because of table layout used in the ip output we get the interface statistics directly from
    // the system. One advantage of this is that it could work when interface is disable.
    exec('cat /sys/class/net/'.RASPI_WIFI_CLIENT_INTERFACE.'/statistics/rx_packets ', $stdoutCatRxPackets);
    $strRxPackets = _('No data');
    if (ctype_digit($stdoutCatRxPackets[0])) {
        $strRxPackets = $stdoutCatRxPackets[0];
    }

    exec('cat /sys/class/net/'.RASPI_WIFI_CLIENT_INTERFACE.'/statistics/tx_packets ', $stdoutCatTxPackets);
    $strTxPackets = _('No data');
    if (ctype_digit($stdoutCatTxPackets[0])) {
        $strTxPackets = $stdoutCatTxPackets[0];
    }

    exec('cat /sys/class/net/'.RASPI_WIFI_CLIENT_INTERFACE.'/statistics/rx_bytes ', $stdoutCatRxBytes);
    $strRxBytes = _('No data');
    if (ctype_digit($stdoutCatRxBytes[0])) {
        $strRxBytes = $stdoutCatRxBytes[0];
        $strRxBytes .= getHumanReadableDatasize($strRxBytes);
    }

    exec('cat /sys/class/net/'.RASPI_WIFI_CLIENT_INTERFACE.'/statistics/tx_bytes ', $stdoutCatTxBytes);
    $strTxBytes = _('No data');
    if (ctype_digit($stdoutCatTxBytes[0])) {
        $strTxBytes = $stdoutCatTxBytes[0];
        $strTxBytes .= getHumanReadableDatasize($strTxBytes);
    }

    define('SSIDMAXLEN', 32);
    // Warning iw comes with: "Do NOT screenscrape this tool, we don't consider its output stable."
    exec('iw dev '.RASPI_WIFI_CLIENT_INTERFACE.' link ', $stdoutIw);
    $stdoutIwAllLinesGlued = implode(' ', $stdoutIw);
    $stdoutIwWRepSpaces = preg_replace('/\s\s+/', ' ', $stdoutIwAllLinesGlued);

    preg_match('/Connected to (([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2}))/', $stdoutIwWRepSpaces, $matchesBSSID) || $matchesBSSID[1] = '';
    $connectedBSSID = $matchesBSSID[1];

    $wlanHasLink = false;
    if ($interfaceState === 'UP') {
        $wlanHasLink = true;
    }

    if (!preg_match('/SSID: ([^ ]{1,'.SSIDMAXLEN.'})/', $stdoutIwWRepSpaces, $matchesSSID)) {
        $wlanHasLink = false;
        $matchesSSID[1] = 'Not connected';
    }

    $connectedSSID = $matchesSSID[1];

    preg_match('/freq: (\d+)/i', $stdoutIwWRepSpaces, $matchesFrequency) || $matchesFrequency[1] = '';
    $frequency = $matchesFrequency[1].' MHz';

    preg_match('/signal: (-?[0-9]+ dBm)/i', $stdoutIwWRepSpaces, $matchesSignal) || $matchesSignal[1] = '';
    $signalLevel = $matchesSignal[1];

    preg_match('/tx bitrate: ([0-9\.]+ [KMGT]?Bit\/s)/', $stdoutIwWRepSpaces, $matchesBitrate) || $matchesBitrate[1] = '';
    $bitrate = $matchesBitrate[1];

    // txpower is now displayed on iw dev(..) info command, not on link command.
    exec('iw dev '.RASPI_WIFI_CLIENT_INTERFACE.' info ', $stdoutIwInfo);
    $stdoutIwInfoAllLinesGlued = implode(' ', $stdoutIwInfo);
    $stdoutIpInfoWRepSpaces = preg_replace('/\s\s+/', ' ', $stdoutIwInfoAllLinesGlued);

    preg_match('/txpower ([0-9\.]+ dBm)/i', $stdoutIpInfoWRepSpaces, $matchesTxPower) || $matchesTxPower[1] = '';
    $txPower = $matchesTxPower[1];

    // iw does not have the "Link Quality". This is a is an aggregate value,
    // and depends on the driver and hardware.
    // Display link quality as signal quality for now.
    $strLinkQuality = 0;
    if ($signalLevel > -100 && $wlanHasLink) {
        if ($signalLevel >= 0) {
            $strLinkQuality = 100;
        } else {
            $strLinkQuality = 100 + $signalLevel;
        }
    }

    $wlan0up = false;
    $classMsgDevicestatus = 'warning';
    if ($interfaceState === 'UP') {
        $wlan0up = true;
        $classMsgDevicestatus = 'success';
    }


    if (isset($_POST['ifdown_wlan0'])) {
        // Pressed stop button
        if ($interfaceState === 'UP') {
            $status->addMessage(sprintf(_('Interface is going %s.'), _('down')), 'warning');
            exec('sudo ip link set '.RASPI_WIFI_CLIENT_INTERFACE.' down');
            $wlan0up = false;
            $status->addMessage(sprintf(_('Interface is now %s.'), _('down')), 'success');
        } elseif ($interfaceState === 'unknown') {
            $status->addMessage(_('Interface state unknown.'), 'danger');
        } else {
            $status->addMessage(sprintf(_('Interface already %s.'), _('down')), 'warning');
        }
    } elseif (isset($_POST['ifup_wlan0'])) {
        // Pressed start button
        if ($interfaceState === 'DOWN') {
            $status->addMessage(sprintf(_('Interface is going %s.'), _('up')), 'warning');
            exec('sudo ip link set ' . RASPI_WIFI_CLIENT_INTERFACE . ' up');
            exec('sudo ip -s a f label ' . RASPI_WIFI_CLIENT_INTERFACE);
            $wlan0up = true;
            $status->addMessage(sprintf(_('Interface is now %s.'), _('up')), 'success');
        } elseif ($interfaceState === 'unknown') {
            $status->addMessage(_('Interface state unknown.'), 'danger');
        } else {
            $status->addMessage(sprintf(_('Interface already %s.'), _('up')), 'warning');
        }
    } else {
        $status->addMessage(sprintf(_('Interface is %s.'), strtolower($interfaceState)), $classMsgDevicestatus);
    }
	exec('cat '.RASPI_DNSMASQ_LEASES.'| grep -E $(arp -i '.$client_iface.' | grep -oE "(([0-9]|[a-f]|[A-F]){2}:){5}([0-9]|[a-f]|[A-F]){2}" | tr "\n" "\|" | sed "s/.$//")', $clients);
	$nbUser = count($clients);

	exec('cat '. RASPI_OPENVPN_CLIENT_CONFIG, $returnClient);
    exec('cat '. RASPI_OPENVPN_SERVER_CONFIG, $returnServer);
    exec('pidof openvpn | wc -l', $openvpnstatus);

    if ($openvpnstatus[0] == 0) {
        $status = '<div class="alert alert-warning">OpenVPN is not running</div><div class="startvpn button-vpn">Se connecter à NordVPN</div>';
    } else {
        $status = '<div class="alert alert-success">OpenVPN is running</div><div class="stopvpn button-vpn">Stop lien VPN</div>';
    }
    ?>

	<div class="dashboard_tft">
		<div class="tile-content">
			<div class="tile-row">
				<div class="tile bg-red">
					Qualité de connexion <div class="gianttext bold"><?php echo htmlspecialchars($strLinkQuality, ENT_QUOTES); ?>%</div>
				</div>
				<div class="tile bg-green">
					Connecté à <div class="bold"><?php echo htmlspecialchars($connectedSSID, ENT_QUOTES); ?></div>
				</div>
			</div>
			<div class="tile-row">
				<div class="tile bg-black flexcenter">
					<div class="stopberry button-vpn"><i class="fa fa-3x fa-power-off"></i></div>
				</div>
				<div class="tile bg-blue flexcenter-column">
					<?php echo $status; ?>
				</div>
			</div>
		</div>
	</div>
	<script>
	/*
		setTimeout(function(){
           window.location.reload(true);
        }, 30000);
		*/
	</script>
    <?php
}



