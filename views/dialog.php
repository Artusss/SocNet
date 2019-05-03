<?php 
require_once '../components/dialog.php';
require_once '../components/left_panel.php';
require_once '../includes/top_pattern.php';
require_once '../includes/top_panel.php';
?>

<div class="container">
  <div class="row">
    <div class="col-3">
      <?php require_once '../includes/left_panel.php'; ?>
    </div>
    <div class="col-9">
      <div class="room_name"><em>Диалог с </em><?=$_GET['room_name']?> :</div>
      <div class="send_message">
        <form action="<?=$_SERVER['SCRIPT_NAME']?>?room_id=<?=$_GET['room_id']?>&room_type=<?=$_GET['room_type']?>&room_name=<?=$_GET['room_name']?>" method="POST">
          <div class="container">
            <div class="row">
              <div class="col-11">
               <textarea name="text" placeholder="Введите сообщение.."></textarea>
             </div>
             <div class="col-1">
              <div>
                <button type="submit" name="doGoAddMessage"><i class="fas fa-share-square"></i></button>
              </div> 
              <div>
                <?php
                if($_GET['room_type'] === 'group'){
                  $friends_not_in_group = getFriendsNotInGroup(); ?>
                  <div class="add_member">
                    <a href="javascript:PopUpShow(1)"><i class="fas fa-plus"></i></a>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div> 
      </form>
    </div>
    <?php if($_GET['room_type'] === 'group'){ ?>
    <div class="forms main_cont" id="popup1" style="display: none">
      <a href="javascript:PopUpHide(1)"><i class="fas fa-times"></i></a>
      <form action="<?=$_SERVER['SCRIPT_NAME']?>?room_id=<?=$_GET['room_id']?>&room_type=<?=$_GET['room_type']?>&room_name=<?=$_GET['room_name']?>" method="POST">
        <p>Add member on the group: </p>
        <select name="member">
          <option disabled selected>Choose member</option>
          <?php
          foreach ($friends_not_in_group as $fr_n_g) { ?>
            <option value="<?= $fr_n_g ?>"><?= $fr_n_g ?></option>
          <?php } ?>
        </select>
        <p><button type="submit" name="doGoAddMember">Add member</button></p>
      </form>
    </div>
    <?php }
    $messages = ORM::Read('messages',
     'room_id=? AND room_type=? ORDER BY pubdate DESC LIMIT 50',
     [$_GET['room_id'], $_GET['room_type']]
   );

    if($_GET['room_type'] === 'group'){
     $groups_id = unserialize($_SESSION['logged_user']['groups_id']);
     foreach ($groups_id as &$v) {
      if($v[0] == $_GET['room_id']){
       $v[1] = date('Y-m-d H:i:s');
       break;
     }
   }
   $_SESSION['logged_user']['groups_id'] = serialize($groups_id);
   ORM::Update('users', 'groups_id=?', 'id=?', [$_SESSION['logged_user']['groups_id'], $_SESSION['logged_user']['id']]);
 }

 foreach($messages as $one_message){
  if(($one_message['readed'] == 0) && ($one_message['sender'] != $_SESSION['logged_user']['login'])){
   ORM::Update('messages', 'readed=?', 'id=?', [1, $one_message['id']]);
 } ?>
 <div class="message main_cont">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <span class="author"><?=$one_message['sender']?> :</span>
      </div>
      <div class="col-6">
        <span class="date"><em><?=$one_message['pubdate']?></em></span>
      </div>
    </div>
    <div class="row">
      <div class="col-11">
        <span class="text"><?=htmlspecialchars($one_message['message'])?></span>
        <span class="status"><?php if(($one_message['readed'] == 0) && ($one_message['sender'] == $_SESSION['logged_user']['login'])){ ?> <i class="fas fa-genderless"></i> <?php } ?></span>
      </div>
      <div class="col-1 delete_message">
        <?php if($one_message['sender'] === $_SESSION['logged_user']['login']){ ?>
         <form action="<?=$_SERVER['SCRIPT_NAME']?>?room_id=<?=$_GET['room_id']?>&room_type=<?=$_GET['room_type']?>&room_name=<?=$_GET['room_name']?>" method="POST">
          <input type="hidden" name="room_type" value="<?=$one_message['room_type']?>">
          <input type="hidden" name="id" value="<?=$one_message['id']?>">
          <button type="submit" name="doGoDeleteMessage"><i class="fas fa-times"></i></button>
        </form>
      <?php } ?>
    </div>
  </div>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
<?php require_once '../includes/bot_pattern.php';