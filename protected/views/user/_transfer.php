<?php ?>
<form method="post">
    <div>
        <div>Перевести деньги пользователю</div>
        <table>
            <tr>
                <td>Получатель:</td>
                <td>
                    <?php                     
                    echo CHtml::dropDownList('Log[to]', $values['to'] ,$usersList,
                        array('empty' => '-Выберите получателя-')); 
                    ?>
                </td>
                <td>
                    <?php
                        if (isset($_errors['to'])){
                            echo implode("; ", $_errors['count']);                            
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Сумма:</td>
                <td>
                    <?php
                    echo CHtml::textField('Log[count]',$values['count']);
                    ?>
                </td>
                <td>
                    <?php
                        if (isset($_errors['count'])){
                            echo implode("; ", $_errors['count']);                            
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php
                        echo CHtml::submitButton('Перевести');
                    ?>
                </td>
                <td></td>
            </tr>
        </table>        
    </div>
</form>

