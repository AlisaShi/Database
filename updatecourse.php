<!DOCTYPE html>
<html lang="zh-en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,
    initial-scale=1.0, user-scalable = no">
    
    <link rel="stylesheet" href="bar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <!-- CDN的方式(直接連到網路) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.1/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <title>aidjobbManagerUpdatePage</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-light  bg-warning">
          <div class="container-fluid">

            <a class="navbar-brand" >
               <img src="img/aidjob.png" alt="" width="60" height="60">
             </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav"  style="float:right">
               <ul class="navbar-nav me-auto">
                <li class="nav-brand">
                  <a class="nav-link active" aria-current="page" href="option.php">使用者頁面</a>
                </li>

                <li class="nav-brand">
                  <a class="nav-link active" aria-current="page" href="updatework.php">工作修改</a>
                </li>

                
                <li class="nav-brand">
                  <a class="nav-link active" aria-current="page" href="updatecourse.php">就業輔導修改</a>
                </li>

                
                <li class="nav-brand">
                  <a class="nav-link active" aria-current="page" href="updateother.php">其他修改</a>
                </li>

                <li class="nav-brand">
                  <a class="nav-link navbar-fixed" href="insertwork.php">新增</a>
                </li>

                <li class="nav-brand">
                  <a class="nav-link navbar-fixed" href="deletework.php">刪除</a>
                </li>
              
              
  
              </ul>
            </div>
          </div>
        </nav>
        <br>

        <section>
        <div class="row">
 
                              <br/>
                              <h3 style="text-align:center">就業輔導修改</h3>
                              <br>
                              <div style="text-align:center">
                                <span >屬性</span>
                                   <select>
                                        <option>選擇屬性</option>
                                   </select>

                                   <span style="text-align:center;display:inline-block">

                                  <form> 
                                      <input  type="search" placeholder="搜尋" aria-label="Search" size="50" onkeyup='saveValue(this);'>
                                        <span >
                                          <button type="button" class="btn btn-outline-warning button">搜尋</button>
                                        </span>
                                </form>

                              </span>
                          </div>

</div>                        
      <div class="row">

                              <div class="form-input mb-3" style="text-align:center" id="worktable">
                                   <div>
                                   <table cellspacing=1px>
                                   <tr>
											<!-- <td>課程ID:</td> -->
											<td>課程名稱:</td>
											<td>登入日期:</td>
											<td>課程日期:</td>
											<td>課程電話:</td>
											<td>課程網站:</td>
                                                       <td>課程價錢:</td>
                                                       <td>課程種類:</td>
                                                       <td>技能:</td>
                                                       <td>課程地區:</td>
                                                       <td>課程地址:</td>

										</tr>

                                                  <!-- <?php
										$num =0;
										$temp=0;
										do{
											?> -->

											<tr>
												<td>
													<input type="text"size='13px' name="course_name[]" value="<?php echo $row['job_name']; ?>"readonly="readonly">
												</td>
												<td>
													<input type="text"size='13px' name="sign_date[]" value="<?php echo $row['job_description']; ?>"readonly="readonly">
												</td>
                                                            <td>
													<input type="text"size='13px' name="course_date[]" value="<?php echo $row['job_description']; ?>"readonly="readonly">
												</td>
                                                            
                                                            <td>
													<input type="text"size='13px' name="course_phone[]" value="<?php echo $row['work_place']; ?>">
												</td>
												
                                                            <td>
													<input type="text"size='13px' name="course_website[]" value="<?php echo $row['work_place']; ?>">
												</td>
                                                            <td>
													<input type="text"size='13px' name="course_price[]" value="<?php echo $row['work_time']; ?>"readonly="readonly">
												</td>
												<td>
                                                                 <select >
                                                                      <option >選擇資料表</option>
                                                                      <option >填寫資料</option>
                                                                 </select>

												</td>
												
                         
												<td> 
                                                                 <select >
                                                                      <option >選擇資料表</option>
                                                                      <option >填寫資料</option>
                                                                 </select>
												</td>
												
												<td>
                                                                 <select >
                                                                 <option >選擇資料表</option>
                                                                 <option >填寫資料</option>
                                                                 </select>
												</td>

												<td>
													<input type="text"size='13px' name="course_address[]" value="<?php echo $row['min_wage']; ?>">
												</td>
                                                            
                                                            
												<td >
												<button type="submit" name="<?php echo $temp?>" style="width:70px" class="btn btn-primary " onclick='onUpdateButton()'>修改</button><br />
											</td>
											</tr>
											<!-- <?php
											$temp+=1;
											$num+=1;
										}while($row=mysqli_fetch_assoc($ro));
										?> -->
									</table>
                                   </div>

                                   

                                   
                              </div>       
                    </div>
               <!-- </div> -->
          </section>
          
     
     </div>
     <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.1/dist/umd/popper.min.js"></script> -->

</body>