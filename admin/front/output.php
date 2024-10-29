<div id="adjustme_app">
    <div id="adjustme_toolbar">
        <ul>
            <?php
            //Todo when got time, make this in Vue, I could not figure out how to make a loop item fire a specific function? n00b
            $toolbar = [
                [
                    'text' => 'Add',
                    'function' => 'place_request()',
                    'id' => 'adjustme_toolbar_add'
                ], [
                    'text' => 'Send to developer',
                    'function' => 'post_list()',
                    'id' => 'adjustme_toolbar_send'
                ],
            ];
            ?>
            <?php foreach ($toolbar as $menu): ?>
                <li id="<?php echo $menu['id'] ?>" class="adjustme_menu">
                    <span class="adjustme_pointer" @click="<?php echo $menu['function'] ?>">
                        <span class="ab-icon"></span>
                        <span class="ab-label"><?php echo $menu['text'] ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
        <span id="adjustme_loading" v-if="loading === 'true'"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></span>
        <span id="adjustme_toolbar_deactivation" class="adjustme_pointer dashicons dashicons-no-alt"
              v-on:click.left="deactivation()"></span>
    </div>
    <ul id="adjustme_list">
        <li v-for="request in list" class="adjustme_request" v-bind:style="request.position" >
            <div class="adjustme_request_bar">
                <div class="adjustme_request_bar_move" v-on:click.left="open_request($event)" v-on:mousedown.left="move_request(request)"></div>
                <span class="adjustme_pointer dashicons dashicons-minus adjustme_request_minimize"
                      v-on:click.left="minimize_request($event)"></span>
                <span class="adjustme_pointer dashicons dashicons-no-alt adjustme_request_remove"
                      v-on:click.left="remove_request(request)"></span>
            </div>
            <textarea v-model="request.text" placeholder="What can I do for you?"></textarea>
        </li>
    </ul>
    <div id="adjustme_notification" v-bind:class="notification.type">
        {{ notification.message }}
    </div>
</div>
