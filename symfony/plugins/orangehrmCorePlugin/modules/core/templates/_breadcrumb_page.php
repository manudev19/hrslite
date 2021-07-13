<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

?>

<style type="text/css">
    .color {
        font-weight: bold;

    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<ul class="breadcrumb">
    <li>
        <a href="/symfony/web/index.php/dashboard">
            <i style="color:black;" class="fa fa-home"></i>

        </a>
    </li>
    <?php foreach ($breadcrumb as $page) {
        if (!empty($page)) {
            if (!empty($page['link'])) { ?>
                <li class='color'>
                    <a href='<?php echo $page['link']; ?>'>
                        <?php echo  $page['name']; ?>
                    </a>
                </li><?php } else if(!empty($page['name'])) { ?>
                <li class='color'>
                    <?php echo  $page['name']; ?>
                </li>
            <?php }
            }
        } ?>
</ul>