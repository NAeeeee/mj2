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