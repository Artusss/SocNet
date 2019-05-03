<?php
require_once '../components/main_page.php';
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
			<div class="container">
				<div class="row">
					<div class="col-4">
						<div class="user_photo main_cont">
							<img src="../includes/img/default-user.png" alt="user_photo">
						</div>
					</div>
					<div class="col-8">
						<div class="user_info main_cont">
							<ul>
								<li>User : <?=$main_user['login']?> <em>#<?=$main_user['id']?></em></li>
								<li><em><?=$main_user['email']?></em></li>
							</ul>
                            <div class="user_about">
                                <?php
                                if($main_user['login'] !== $_SESSION['logged_user']['login']){
                                    if(checkFriend($main_user['login'])){ ?>
                                        <p>Пользователь <?=$main_user['login']?> у вас в друзьях</p>
                                        <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                            <button type="submit" name="doGoDeleteFriend">Удалить из друзей</button>
                                        </form>
                                        <?php
                                    }else if(checkOffer('OfferFriend', $main_user)){ ?>
                                        <p>Ожидается ответ пользователя <?=$main_user['login']?> на запрос в друзья</p>
                                        <?php
                                    }else{ ?>
                                        <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                            <button type="submit" name="doGoAddNotice">Заявка в друзья</button>
                                        </form>
                                    <?php }
                                }else{ ?>
                                    <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                        <button type="submit" name="doGoLogout">Выйти</button>
                                    </form>
                                <?php } ?>
                            </div>
						</div>
                    </div>
                    <div class="col-12">
                        <div class="records">
                            <?php if($main_user['login'] === $_SESSION['logged_user']['login']){ ?>
                                <div class="create_record main_cont">
                                    <p>Add record:</p>
                                    <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                        <div class="row">
                                            <div class="col-9">
                                                <input type="text" name="text" placeholder=" Введите текст..">
                                            </div>
                                            <div class="col-3">
                                                <button type="submit" name="doGoCreateRecord">Опубликовать</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php }
                            $records = getRecord();
                            foreach ($records as $one_record){ ?>
                                <div class="message record main_cont">
                                    <div class="row">
                                        <div class="col-6">
                                            <span class="author"><?=$one_record['author_name']?></span>
                                        </div>
                                        <div class="col-6">
                                            <span class="date"><em><?=$one_record['pubdate']?></em></span>
                                        </div>
                                        <div class="col-11"><span class="text"><?=$one_record['text']?> </span></div>
                                        <div class="col-1 delete_message">
                                            <?php if($one_record['author_name'] === $_SESSION['logged_user']['login']){ ?>
                                                <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                                    <input type="hidden" name="record_id" value="<?=$one_record['id']?>">
                                                    <button type="submit" name="doGoDeleteRecord"><i class="fas fa-times"></i></button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="likes">
                                        <div class="row">
                                            <div class="col-6">
                                                <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" id="set_like" method="POST">
                                                    <input type="hidden" name="record_id" value="<?=$one_record['id']?>">
                                                    <button type="submit" name="doSetLike">
                                                        <?= getLike_count($one_record['id']) ?>
                                                        <i class="fas fa-heart"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="show_comments col-6">
                                                <a href="javascript:PopUpShow(<?=$one_record['id']?>)"><i class="fas fa-comment"></i></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="comments" id="popup<?=$one_record['id']?>" style="display: none">
                                        <div class="hide_comments"><a href="javascript:PopUpHide(<?=$one_record['id']?>)"><i class="fas fa-times"></i></a></div>
                                        <div class="create_comment main_cont">
                                            <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                                <input type="hidden" name="record_id" value="<?=$one_record['id']?>">
                                                <div class="row">
                                                    <div class="col-11">
                                                        <input type="text" name="text" placeholder=" Введите текст..">
                                                    </div>
                                                    <div class="col-1">
                                                        <button type="submit" name="doGoAddComment"><i class="fas fa-share-square"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <?php
                                        $comments = getComment($one_record['id']);
                                        foreach ($comments as $one_comment){ ?>
                                            <div class="message main_cont">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <span class="author"><?=$one_comment['author']?></span>
                                                    </div>
                                                    <div class="col-6">
                                                        <span class="date"><em><?=$one_comment['pubdate']?></em></span>
                                                    </div>
                                                    <div class="col-11">
                                                        <span class="text"><?=$one_comment['text'] ?></span>
                                                    </div>
                                                    <div class="col-1 delete_message">
                                                        <?php if($one_comment['author'] === $_SESSION['logged_user']['login']){ ?>
                                                            <form action="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=$_GET['id']?>" method="POST">
                                                                <input type="hidden" name="comment_id" value="<?=$one_comment['id']?>">
                                                                <button type="submit" name="doGoDeleteComment"><i class="fas fa-times"></i></button>
                                                            </form>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require_once '../includes/bot_pattern.php';
