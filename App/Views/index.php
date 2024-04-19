<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Certificate generator</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet">
        <style>
            .form-control::placeholder {opacity: .5;}
        </style>
    </head>
    <body style="padding: 2rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-6">
                    <h3 class="mb-4">Генерувати сертифікат</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Ім'я учня <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" name="name" value="<?= $values['name'] ?? '' ?>" minlength="3" maxlength="25" placeholder="Максим Жатков" required />
                            <?php if ($errors['name'] ?? false) : ?><div class="small text-danger"><?= $errors['name'] ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="curs" class="form-label">Назва курсу <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" name="curs" value="<?= $values['curs'] ?? '' ?>" minlength="5" maxlength="50" placeholder="CS50: Вступ до штучного інтелекту з Python" required />
                            <?php if ($errors['curs'] ?? false) : ?><div class="small text-danger"><?= $errors['curs'] ?></div><?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="date" class="form-label">Дата завершення курсу <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" name="date" id="date" value="<?= $values['date'] ?? '' ?>" placeholder="<?= date('d.m.Y') ?>" required />
                            <?php if ($errors['date'] ?? false) : ?><div class="small text-danger"><?= $errors['date'] ?></div><?php endif; ?>
                        </div>
                        <input type="hidden" class="form-control" name="token" value="<?= $token ?>" />
                        <button type="submit" class="btn btn-primary">Генерувати</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script -->
        <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.uk.min.js" charset="UTF-8"></script>
        <script src="/assets/js/index.js"></script>
    </body>
</html>