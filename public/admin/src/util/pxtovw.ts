import type { Plugin } from "postcss";
export const options = {
  basesize: 1, // 控制转换比例  默认 1
  viewportWidth: 375, // 设计稿宽度，默认 375
};
interface Options {
  basesize: number;
  viewportWidth: number;
}
export const pxtovw = (option: Options = options): Plugin => {
  return {
    postcssPlugin: "px-to-vw",
    // 这是 postcss 提供的一个 API，用以获取所有的 css 节点
    // node.prop 属性名  node.value 属性值
    Declaration(node) {
      if (node.value.includes("px")) {
        node.value = numInit(node.value, option);
      }
    },
  };
};

// 把字符串中的 px 全部转化为 vw，其他的不动
// 实现方式有很多，你也可以定义属于你自己的方法
export function numInit(str: string, option: Options): string {
  if (str.includes("px")) {
    const opt = Object.assign({}, option);
    let vwVal = "",
      number = "0123456789p",
      pxVal = "";
    for (let v of str) {
      if (number.includes(v)) pxVal += v;
      else if (pxVal.length > 1 && v == "x") {
        let base = Number(pxVal.slice(0, -1)) * opt.basesize;
        vwVal += ((base / opt.viewportWidth) * 100).toFixed(2) + "vw";
        pxVal = "";
      } else {
        if (pxVal.length) {
          vwVal += pxVal;
          pxVal = "";
        }
        vwVal += v;
      }
    }
    return vwVal + pxVal;
  } else return str;
}

// 特殊处理行内样式和另外一些不得不用 px 作为单位的数值
export function rem(num: number): number {
  let clientWidth =
    window.innerWidth ||
    document.documentElement.clientWidth ||
    document.body.clientWidth;
  if (!clientWidth) return 0;
  let fontSize = clientWidth / options.viewportWidth;
  return num * fontSize * options.basesize;
}
