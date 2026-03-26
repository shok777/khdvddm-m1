<?php

    
                        <!-- Проектов  нет -->
                        <p class="text-muted">Проекты еще не добавлены.</p>
            <?php else: ?>
                        <div class="row g-3">
                                <?php foreach ($projects as $el): ?>
                                        <!-- Список проектов -->
                                        <div class="col-4">
                                            <div class="card border-dark">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= $el['title'] ?></h5>
                                                    <p class="card-text text-muted"><?= $el['description'] ?></p>
                                                    <a href="<?= $el['url'] ?>" target="_blank" class="btn btn-sm btn-outline-dark">Открыть</a>
                                                </div>
                                                <div class="card-footer">
                                                    <form method="POST" action="adminpanel.php" class="mb-0">
                                                        <input type="hidden" name="project_id" value="<?= $el['id'] ?>">
                                                        <button type="submit" name="delete_project" class="btn btn-sm btn-danger">🗑 Удалить</button>  
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                        </div>
            <?php endif; ?>
            
                                    
           
           
            




            
            
        </div>
    </div>




    <br><br>
    <script src="Bootstrap\bootstrap.js"></script>
</body>
</html>
