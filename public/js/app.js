// import './bootstrap';

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();


function dateFormat(s)
{
    if( s==null ) return "";
    s = s.replace(/-/gi, "");
    return s.substr(0,4) + "." + s.substr(4,2) + "." + s.substr(6,2);
}

function timeFormat(s)
{
    if( s==null ) return "";
    return s.substr(0,2) + ":" + s.substr(2,2) + ":" + s.substr(4,2);
}

function numberFormat(n)
{
    var regexp = /\\B(?=(\\d{3})+(?!\\d))/g;
    return n.toString().replace(regexp, ',');
}

function rateFormat(n)
{
    var nums = n.split(".");
    if( nums.length == 2 )
    {
        if( nums[1].length==1 )
        {
            nums[1] += "0";
        }
        else if( nums[1].length>2 )
        {
            nums[1] = nums[1].substr(0,2);
        }
        return nums.join('.');
    }
    else
    {
        return nums + ".00";
    }
}

function rateFormat1(n)
{
    var nums = n.split(".");
    if( nums.length == 2 )
    {
        if( nums[1].length==0 )
        {
            nums[1] += "0";
        }
        else if( nums[1].length>1 )
        {
            nums[1] = nums[1].substr(0,1);
        }
        return nums.join('.');
    }
    else
    {
        return nums + ".0";
    }
}

// 문자만 입력
function onlyString(obj)
{
    obj.value = obj.value.replace(/[^ㄱ-힣]/g, "");
}

// 문자,영문
function onlyStr(obj)
{
	obj.value = obj.value.replace(/[^ㄱ-힣a-zA-Z ]/g, "");

	//	한글이 있으면 띄어쓰기 금지
	if( /[ㄱ-힣]/.test(obj.value) )
	{
		obj.value = obj.value.replace(/[^ㄱ-힣a-zA-Z]/g, "");
	}
}

// 숫자만 입력
function onlyNumber(obj)
{
    obj.value = obj.value.replace(/[^0-9]/g, "");
}

// 숫자,영문
function onlyEngNum(obj)
{
    obj.value = obj.value.replace(/[^0-9a-zA-Z]/g, "");
}

// 이메일
function onlyEmail(obj)
{
    obj.value = obj.value.replace(/[^0-9a-zA-Z@.]/g, "");
}

// 비밀번호 !@#$%^&*()_+-=[]{};':"\\|,.<>/?
function onlyEngNum(obj)
{
    obj.value = obj.value.replace(/[^a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g, "");
}

// 공용 팝업
function alertc(title, contents='', mh='')
{
    $("#global-alert-title").html(title);
    $("#alertModalMsg").html(contents);

    $("#modal-header-s").removeClass('bg-danger');
    $("#modal-header-s").removeClass('bg-primary');
    if( mh == 'p' )
    {
        $("#modal-header-s").addClass('bg-primary');
    }
    else
    {
        $("#modal-header-s").addClass('bg-danger');
    }

    const modalElement = document.getElementById("alertModal");

    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// 삭제 확인 팝업
function confirmDelete(actionUrl, method='') 
{
    const form = document.getElementById('deleteForm');
    form.action = actionUrl;
    if( method === 'GET' )
    {
        form.method = 'GET';
    }
    else
    {
        form.method = 'POST';
    }

    const modal = new bootstrap.Modal(document.getElementById('delModal'));
    modal.show();
}

// 탈퇴 확인 팝업
function confirmWithdraw(url) 
{
    const form = document.getElementById('withdrawForm');
    form.action = url;
    form.method = 'POST';

    const modal = new bootstrap.Modal(document.getElementById('withdrawModal'));
    modal.show();
}


// 첨부파일 용량 체크
function checkFiles() 
{
    const allowedTypes = ['image/jpeg', 'image/png'];
    const maxSize = 3 * 1024 * 1024; // 3MB
    const inputs = [file, file2, file3];
    let errors = [];

    inputs.forEach(input => {
        if (!input) return;
        for (let file of input.files) {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`${file.name} : 허용되지 않는 형식`);
            }
            if (file.size > maxSize) {
                errors.push(`${file.name} : 3MB 초과`);
            }
        }
    });

    if (errors.length > 0) {
        alertc('업로드 문제 발생', errors.join('<br>'));
        return false;
    }

    return true;
}

function checkFiles_edit()
{
    const allowedTypes = ['image/jpeg', 'image/png'];
    const maxSize = 3 * 1024 * 1024; // 3MB

    const fileInputs = document.querySelectorAll('input[name="file[]"]');
    let errors = [];

    fileInputs.forEach(input => {
        if (!input.files.length) return; // 파일 선택 안했으면 건너뜀
        for (let file of input.files) {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`${file.name} : 허용되지 않는 형식`);
            }
            if (file.size > maxSize) {
                errors.push(`${file.name} : 3MB 초과`);
            }
        }
    });

    if (errors.length > 0) {
        alertc('업로드 문제 발생',errors.join('<br>'));
        return false;
    }

    return true;
}


function upload(mode='')
{
    var d = document.getElementById('div').value;
    var t = document.getElementById('title').value;
    var c = document.getElementById('content').value;

    // 검사
    if (d === '') {
        alertc('확인 요청','항목을 선택해주세요.');
        return false;
    }
    else if (t === '') {
        alertc('확인 요청','제목을 입력해주세요.');
        return false;
    }
    else if ( c === '' ) {
        alertc('확인 요청','내용을 입력해주세요.');
        return false;
    }

    // 파일 용량/타입 체크
    var atype = ['image/jpeg', 'image/png'];
    var max = 3 * 1024 * 1024; // 2MB

    if( mode == 'create' )
    {
        // 게시물 등록
        if (!checkFiles()) {
            return false;
        }
    }
    else
    {
        // 수정
        if (!checkFiles_edit()) {
            return false;
        }
    }

    console.log('조건모두통과');
    document.getElementById('Form').submit();
}