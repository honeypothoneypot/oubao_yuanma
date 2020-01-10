/**
 * 获取本周、本季度、本月、上月的开始日期、结束日期
getFullYear() ：返回Date对象的年份值；4位年份。

getMonth() ：返回Date对象的月份值。从0开始，所以真实月份=返回值+1 。

getDate() ：返回Date对象的月份中的日期值；值的范围1~31 。

getHours() ：返回Date对象的小时值。

getMinutes() ：返回Date对象的分钟值。

getSeconds() ：返回Date对象的秒数值。

getMilliseconds() ：返回Date对象的毫秒值。

getDay() ：返回Date对象的一周中的星期值；0为星期天，1为星期一、2为星期二，依此类推

getTime() ：返回Date对象与'1970/01/01 00:00:00'之间的毫秒值(北京时间的时区为东8区，起点时间实际为：'1970/01/01 08:00:00') 。
 */
var now = new Date(); //当前日期
var nowYear = now.getFullYear(); //当前年.获取完整的年份(4位,1970-????)
var nowMonth = now.getMonth(); //当前月.获取当前月份(0-11,0代表1月)
var nowDay = now.getDate(); //当前日.获取当前日(1-31)
var nowDayOfWeek = now.getDay(); //今天本周的第几天.//获取当前星期X(0-6,0代表星期天)
var week = now.getDay();
//一天的毫秒数
var millisecond=1000*60*60*24;
function formatDateNew(type){
    var startStop = new Array();
    switch(type){
        case 'benzhou':
            //减去的天数
            var minusDay=week!=0?week-1:6;
            //增加的天数
            var plusDay=week==0?0:7-week;
            //本周 周一
            var start=new Date(now.getTime()-(minusDay*millisecond));
            //本周 周日
            var end=new Date(now.getTime()+(plusDay*millisecond));
            break;
        case 'shangzhou':
            //减去的天数
            var minusDay=week!=0?week-1:6;
            //获得当前周的第一天
            var currentWeekDayOne=new Date(now.getTime()-(millisecond*minusDay));
            //上周最后一天即本周开始的前一天
            var end = new Date(currentWeekDayOne.getTime()-millisecond);
            //上周的第一天
            var start = new Date(end.getTime()-(millisecond*6));
            break;
        case 'zuotian':
            //减去的天数
            var minusDay=1;
            //增加的天数
            var plusDay=week==0?0:7-week;
            //本周 周一
            var start=new Date(now.getTime()-(minusDay*millisecond));
            //本周 周日
            var end=new Date(now.getTime());
            break;
        case 'jintian':
            //减去的天数
            var minusDay=0;
            //增加的天数
            var plusDay=week==0?0:7-week;
            //本周 周一
            var start=new Date(now.getTime()-(minusDay*millisecond));
            //本周 周日
            var end=new Date(now.getTime());
            break;
        case 'shangyue':
            //获得上一个月的第一天
            var start = getPriorMonthFirstDay(nowYear,nowMonth);
            //获得上一月的最后一天
            var end = new Date(start.getFullYear(),start.getMonth(),getMonthDays(start.getFullYear(), start.getMonth()));
            break;
        case 'benyue':
            //求出本月第一天
            var start = new Date(nowYear,nowMonth,1);
            var nowYear2 = '';
            var nowMonth2 = '';
            //当为12月的时候年份需要加1
            //月份需要更新为0 也就是下一年的第一个月
            if(nowMonth==11){
                nowYear2 = nowYear+1;
                nowMonth2 = 0;
            }else{
                //否则只是月份增加,以便求的下一月的第一天
                nowYear2 = nowYear;
                nowMonth2  = nowMonth+1;
            }
            //下月的第一天
            var nextMonthDayOne = new Date(nowYear2,nowMonth2,1);
            //求出上月的最后一天
            var end = new Date(nextMonthDayOne.getTime()-millisecond);
            break;
        case 'qunian':
            var currentYear = nowYear;
            currentYear--;
            var start = new Date(currentYear,0,1);
            var end = new Date(currentYear,11,31);
            break;
        case 'bennian':
            var start = new Date(nowYear,0,1);
            var end = new Date(nowYear,11,31);
            break;
    }
    start = formatDate(start);
    end = formatDate(end);
    startStop.push(start);//
    startStop.push(end);//终止时间
    //返回
    return startStop;
}
//得到上个月的第一天
var getPriorMonthFirstDay = function(year,month){
    //年份为0代表,是本年的第一月,所以不能减
    if(month==0){
        month=11;//月份为上年的最后月份
        year--;//年份减1
        return new Date(year,month,1);
    }
    //否则,只减去月份
    month--;
    return new Date(year,month,1);;
};
/**
 * 获得该月的天数
 * @param year年份
 * @param month月份
 * */
var getMonthDays = function(year,month){
    //本月第一天 1-31
    var relativeDate=new Date(year,month,1);
    //获得当前月份0-11
    var relativeMonth=relativeDate.getMonth();
    //获得当前年份4位年
    var relativeYear=relativeDate.getFullYear();

    //当为12月的时候年份需要加1
    //月份需要更新为0 也就是下一年的第一个月
    if(relativeMonth==11){
        relativeYear++;
        relativeMonth=0;
    }else{
        //否则只是月份增加,以便求的下一月的第一天
        relativeMonth++;
    }
    //下月的第一天
    var nextMonthDayOne=new Date(relativeYear,relativeMonth,1);
    //返回得到上月的最后一天,也就是本月总天数
    return new Date(nextMonthDayOne.getTime()-millisecond).getDate();
};
//格式化日期：yyyy-MM-dd
function formatDate(date) {
    var myyear = date.getFullYear();
    var mymonth = date.getMonth() + 1;
    var myweekday = date.getDate();
    if (mymonth < 10) {
        mymonth = "0" + mymonth;
    }
    if (myweekday < 10) {
        myweekday = "0" + myweekday;
    }
    return (myyear + "-" + mymonth + "-" + myweekday);
}