<?php

namespace App\Http\Controllers;

use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\TimeRegister;
use App\Models\UserSubject;
use App\Models\Subjects;
use App\Models\SubjectRegister;
use App\Models\Semester;
use App\Models\SemesterSubjects;
use App\Models\Year;
use App\Models\SubjectGroup;
use App\Models\Rate;
use App\Models\Classroom;
use App\Models\UserAdmin;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Callout;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class SubjectRegisterController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');

            $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return User::grid(Subjects::class, function (Grid $grid) {
            $grid->registerColumnDisplayer();
             $user = Auth::user();
             $schoolYearUser = $user->school_year;
             //check school year
             $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
//             $schoolYearUser = (string) $schoolYearUser;
             if($timeRegister) {
//                 //get school year in time register and school year = "ALL"
//                 if(in_array($schoolYearUser, $timeRegister->school_year) || $timeRegister->school_year['0'] == "All") {
//                 } else {
//                     $grid->model()->where('id', -1);
//                 }

                 //open subject register follow semester
                 $nameSemester = $timeRegister->semester;
                 $idSemester = Semester::where('name', $nameSemester)->pluck('id');
                 $subjects_id = SemesterSubjects::whereIn('semester_id', $idSemester)->orderBy('semester_id', 'DESC')->pluck('subjects_id')->toArray();
                 //sort follow semester
                 $field = '';
                 foreach ($subjects_id as $id) {
                     $field .= ($id . ',');
                 }
                 $field = substr( $field , 0, strlen($field)-1);
                 //get subject user learned
                 $idSubjectRegister = ResultRegister::where('id_user_student', $user->id)->where('is_learned', 1)->pluck('id_subject_register')->toArray();
                 $idSubjectLearned = SubjectRegister::whereIn('id', $idSubjectRegister)->pluck('id_subjects')->toArray();
                 //show subject not learned and subjects in semester in time register (hiển thị các môn chưa học & trong đợt đăng kí đang mở)
                 $grid->model()->whereIn('id', $subjects_id)->whereNotIn('id', $idSubjectLearned)->orderBy(DB::raw('FIELD(id, '. $field .')'));
             }
             $grid->id('id');
            $grid->subject_code('Mã môn học');
             $grid->name('Tên môn học')->display(function ($name){
                 return  '<a href="/user/subject-register/' . $this->id . '/details"  target="_blank" >'.$name.'</a>';
             });

              $grid->credits('Số tín chỉ');
              $grid->credits_fee('Số tín chỉ học phí');
             $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map( function ($arraySemester){
                    $nameSemester = Semester::find($arraySemester)->name;
                    switch ($nameSemester) {
                        case 0 :
                            $nameSemester = 'Học kì hè';
                            break;
                        case 1:
                            $nameSemester = 'Học kì 1';
                            break;
                        case 2:
                            $nameSemester = 'Học kì 2';
                    }
                    $year = Semester::find($arraySemester)->year()->get();
                    $nameYear = $year['0']->name;
                    return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                }, $arraySemester);
                return join('&nbsp;', $name);
             });

//            $grid->column('Chọn lớp')->display(function (){
//            })->editable('select', function (){
//                return SubjectRegister::where('id_subjects', $this->id)->pluck('code_subject_register', 'id')->toArray();
//            });

            $grid->options()->select(SubjectRegister::all()->pluck('code_subject_register', 'id')->toArray());

//            $grid->title()->editable(SubjectRegister::where('id_subjects', $this->grid()->id)->pluck('code_subject_register', 'id'));
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
        });
    }
    protected function form()
    {
        return User::form(Subjects::class, function (Form $form) {
        	$form->registerBuiltinFields();
            // $form->text('subject_code', 'Mã môn học')->readOnly()->rules(function ($form){
            //     return 'required|unique:subjects,subject_code,'.$form->model()->id.',id';
            // });
            $form->text('name','Tên môn học')->rules('required')->readOnly();
            $form->number('credits','Tín chỉ')->rules('integer|min:1|max:6')->readOnly();
            // $form->number('credits_fee', 'Tín chỉ học phí')->rules('integer|min:1|max:12');
			//$form->select('id_semester', 'Học kỳ')->options(Semester::all()->pluck('name', 'id'));
            $form->multipleSelect('subject_group', 'Nhóm môn')->readOnly()->options(SubjectGroup::all()->pluck('name', 'id'))->rules('required');
            $form->disableReset();

            $form->tools(function (Form\Tools $tools) {
			    // Disable list btn
			    $tools->disableListButton();
			    $tools->disableBackButton();
			});
            // $rates = Rate::all();
            // $arrayRate = [];
            // foreach($rates as $rate) {
            //     $arrayRate += [$rate['id'] => $rate['attendance'] . '-'.  $rate['midterm'] .'-' .$rate['end_term']];
            // }
            // $form->select('id_rate', 'Tỷ lệ điểm')->options($arrayRate)->rules('required');
        });
    }
    protected function gridSubjectRegister($idSubjects)
    {


        return User::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->where('id_Subjects', $idSubjects);
             $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần');
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
            $grid->id_classroom('Phòng học')->display(function ($id_classroom){
                if($id_classroom){
                    return Classroom::find($id_classroom)->name;
                } else {
                    return '';
                }
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher){
                if($id_user_teacher){
                    $teacher = UserAdmin::find($id_user_teacher);
                    if($teacher){
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');
            $grid->qty_max('Số lượng tối đa');
            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                //button Register (nút đăng kí)
                $actions->append('<a href="javascript:void(0);" data-id="'.$this->getKey().'"  class="btn btn-primary btnRegister"><i class="glyphicon glyphicon-pencil"></i> &nbsp Đăng ký </a>');
            });
            $registerConfirm = trans('Bạn có chắc chắn muốn đăng ký không?');
            $confirm = trans('Đăng ký');
            $cancel = trans('Hủy bỏ');
            $script = <<<SCRIPT
//            $('.grid-refresh').css('display','none');
$('.btnRegister').unbind('click').click(function() {
    var id = $(this).data('id');
    swal({
      title: "$registerConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3c8dbc",
      confirmButtonText: "$confirm",
      closeOnConfirm: false,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'get',
            url: '/user/subject-register/' + id + '/result-register',
            data: {
                _method:'resultRegister',
                _token:LA.token,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
//                        swal(data.message, '', 'success');
                         swal({
                              title: "Đăng ký thành công", 
                              type: "success"
                             },function() {
                              location.reload();
                         });
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            }
        });
    });
});
SCRIPT;
            User::script($script);
        });
    }
    public function details($id){
        return User::content(function (ContentUser $content) use ($id) {
            $subject = Subjects::findOrFail($id);
            $content->header('Môn học');
            $content->description($subject->name);
            $content->body($this->detailsView($id));
        });
    }
    public function detailsView($id){
        $form = $this->form()->view($id);
        $gridSubject_Register = $this->gridSubjectRegister($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'User.SubjectRegister.info',
                'form' => $form,
                'gridSubjectRegister' => $gridSubject_Register

            ]
        );
    }
    public function resultRegister(Request $request){
        $idSubjecRegister = $request->id;
        $user = Auth::user();
        $idUser = $user->id;
        $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
        $idTimeRegister = $timeRegister->id;

        //get qty current
        $subjecRegister = SubjectRegister::find($idSubjecRegister);
        $qtyCurrent = $subjecRegister->qty_current;
        $qtyMax = $subjecRegister->qty_max;


        //nếu đã đăng kí rồi thì không được đăng kí nữa
        $idSubjects = SubjectRegister::where('id',$idSubjecRegister)->pluck('id_subjects')->toArray();
        $countSubject = ResultRegister::where('id_subject', $idSubjects['0'])->where('time_register', $idTimeRegister)->get()->count();

        if($countSubject >= 1) {
                return response()->json([
                    'status'  => false,
                    'message' => trans('Bạn đã đăng kí môn học này'),
                ]);

        }


        //lấy số lượng chỉ được đăng kí tối đa
        $creditsMax = $timeRegister->credits_max;
        $id_subject_register = ResultRegister::where('time_register', $idTimeRegister)->where('is_learned', 0)->pluck('id_subject_register')->toArray();
//        dd($id_subject_register);

        //nếu số lượng hiện tại lớn hơn số lượng max thì không được đăng kí
        if($qtyCurrent >= $qtyMax) {
            return response()->json([
                'status'  => false,
                'message' => trans('Đăng ký không thành công'),
            ]);
        } else {
            $resultRegister = new ResultRegister;
            $resultRegister->id_user_student = $idUser;
            $resultRegister->id_subject_register = $idSubjecRegister;
            $idSubjects = SubjectRegister::find($idSubjecRegister)->id_subjects;
            $resultRegister->id_subject = $idSubjects;
            $resultRegister->is_learned = 0;
            $resultRegister->time_register = $idTimeRegister;
            if($resultRegister->save()) {
                $subjecRegister->qty_current = $qtyCurrent + 1;
                if($subjecRegister->save()) {
                    return response()->json([
                        'status'  => true,
                        'message' => trans('Đăng ký thành công'),
                    ]);
                }else {
                    return response()->json([
                        'status'  => false,
                        'message' => trans('Đăng ký không thành công'),
                    ]);
                }
            }
        }


    }
}
