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
    let regexp = /\\B(?=(\\d{3})+(?!\\d))/g;
    return n.toString().replace(regexp, ',');
}

function rateFormat(n)
{
    let nums = n.split(".");
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
    let nums = n.split(".");
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
window.onlyString = function (obj)
{
    obj.value = obj.value.replace(/[^ㄱ-힣]/g, "");
}

// 문자,영문
window.onlyStr = function (obj) 
{
	obj.value = obj.value.replace(/[^ㄱ-힣a-zA-Z ]/g, "");

	//	한글이 있으면 띄어쓰기 금지
	if( /[ㄱ-힣]/.test(obj.value) )
	{
		obj.value = obj.value.replace(/[^ㄱ-힣a-zA-Z]/g, "");
	}
}

// 숫자만 입력
window.onlyNumber = function (obj)
{
    obj.value = obj.value.replace(/[^0-9]/g, "");
}

// 숫자,영문
window.onlyEngNum = function (obj) 
{
    obj.value = obj.value.replace(/[^0-9a-zA-Z]/g, "");
}

// 이메일
window.onlyEmail = function (obj)
{
    obj.value = obj.value.replace(/[^0-9a-zA-Z@.]/g, "");
}


// 공용 팝업
window.alertc = function (title, contents='', mh='')
{
    $("#global-alert-title").html(title);
    $("#alertModalMsg").html(contents);

    $("#modal-header-s").removeClass('bg-danger');
    $("#modal-header-s").removeClass('bg-primary');
    $("#modal-header-s").removeClass('bg-secondary');
    if( mh === 'p' )
    {
        $("#modal-header-s").addClass('bg-primary');
    }
    else if( mh === 's' )
    {
        $("#modal-header-s").addClass('bg-secondary');
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
window.confirmDelete = function (actionUrl, param='', method='')
{
    const form = document.getElementById('deleteForm');
    form.action = actionUrl;
    form.method = method === 'GET' ? 'GET' : 'POST';
    // div
    document.getElementById('delete_div').value = param;

    const modal = new bootstrap.Modal(document.getElementById('delModal'));
    modal.show();
}

// 탈퇴 확인 팝업
window.confirmWithdraw = function (url)
{
    const form = document.getElementById('withdrawForm');
    form.action = url;
    form.method = 'POST';

    const modal = new bootstrap.Modal(document.getElementById('withdrawModal'));
    modal.show();
}


// 첨부파일 용량 체크
window.checkFiles = function ()
{
    const allowedTypes = ['image/jpeg', 'image/png'];
    const maxSize = 4 * 1024 * 1024;
    const inputs = [file, file2, file3];
    let errors = [];

    inputs.forEach(input => {
        if (!input) return;
        for (let file of input.files) {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`${file.name} : 확장자를 확인해주세요.(첨부 가능한 확장자 : jpg, png)`);
            }
            if (file.size > maxSize) {
                errors.push(`${file.name} : 4MB 까지만 첨부 가능합니다.`);
            }
        }
    });

    if (errors.length > 0) {
        alertc('업로드 문제 발생', errors.join('<br>'));
        return false;
    }

    return true;
}

window.checkFiles_edit = function ()
{
    const allowedTypes = ['image/jpeg', 'image/png'];
    const maxSize = 4 * 1024 * 1024;

    const fileInputs = document.querySelectorAll('input[name="file[]"]');
    let errors = [];

    fileInputs.forEach(input => {
        if (!input.files.length) return; // 파일 선택 안했으면 건너뜀
        for (let file of input.files) {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`${file.name} : 허용되지 않는 형식`);
            }
            if (file.size > maxSize) {
                errors.push(`${file.name} : 4MB 초과`);
            }
        }
    });

    if (errors.length > 0) {
        alertc('업로드 문제 발생',errors.join('<br>'));
        return false;
    }

    return true;
}


window.upload = function (mode='')
{
    let d = document.getElementById('div').value;
    let t = document.getElementById('title').value;
    let c = document.getElementById('content').value;

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

    if( mode == 'notice' )
    {
        let ss = document.getElementById('save_status').value;
        let iv = document.getElementById('is_visible').value;

        if ( ss === '' ) {
            alertc('확인 요청','저장 상태를 선택해주세요.');
            return false;
        }
        else if ( iv === '' ) {
            alertc('확인 요청','노출 여부를 선택해주세요.');
            return false;
        }

        // if( ss === 'N' && iv === 'Y' )
        // {
        //     alertc('확인 요청',"삭제 상태인 글은 활성화할 수 없습니다.");
        //     return false;
        // }
    }

    // 파일 용량/타입 체크
    let atype = ['image/jpeg', 'image/png'];
    let max = 3 * 1024 * 1024; // 2MB

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


window.searchSumbit = function ()
{
    let search = document.getElementById('search').value;

    if( search == '' )
    {
        alertc('확인 요청', '검색어를 입력해주세요.');
        return false;
    }

    var f = $("#searchForm");
    f.submit();
}
