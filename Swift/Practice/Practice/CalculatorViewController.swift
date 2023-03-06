//
//  CalculatorViewController.swift
//  Practice
//
//  Created by Tanaka Lab on R 4/05/02.
//

// フレームワークのインポート
import UIKit

// クラスの継承
class CalculatorViewController: UIViewController {
    
    // enum -> 列挙型
    enum CalculateStatus {
        case none, plus, minus, multiplication, division
    }
    
    var firstNumber = ""
    var secondNumber = ""
    var calculateStatus: CalculateStatus = .none
    
    let numbers = [
        ["C", "%", "$","÷"],
        ["7", "8", "9","×"],
        ["4", "5", "6","-"],
        ["1", "2", "3","+"],
        ["0", ".", "="]
    ]
    
    @IBOutlet weak var numberLabel: UILabel!
    @IBOutlet weak var calculatorCollectionView: UICollectionView!
    @IBOutlet weak var calculatorHeightConstraint: NSLayoutConstraint!
    
    // UIViewControllerのviewDidLoadをオーバーライド
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        
        // delegate -> コントロール権限を渡す
        // datasource -> データを渡す権限
        calculatorCollectionView.delegate = self
        calculatorCollectionView.dataSource = self
        // register -> idが""内のcellを再利用する
        calculatorCollectionView.register(CalculatorViewCell.self, forCellWithReuseIdentifier: "cellIdentifier")
        // collectionViewの大きさを調整
        calculatorHeightConstraint.constant = view.frame.width * 1.4
        // ボタン配置部分を透明にして背景を映す
        calculatorCollectionView.backgroundColor = .clear
        // init -> インスタンスを初期化
        calculatorCollectionView.contentInset = .init(top: 0, left: 14, bottom: 0, right: 14)
        numberLabel.text = "0"
        view.backgroundColor = .black
    }
    // 電卓のCを押したときのメソッド
    func clear() {
        firstNumber = ""
        secondNumber = ""
        numberLabel.text = "0"
        calculateStatus = .none
    }
}

// extention -> CalculatorViewControllerの機能を拡張
extension CalculatorViewController: UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout {
    // numbersの数だけラベルを表示
    // -> 戻り値の型
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        return numbers.count
    }
    
    // collectionViewのcellの数を返す
    // _ -> メソッドを呼ぶとき引数に_の後ろを付けなくても良い
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return numbers[section].count
    }
    
    // cellの大きさやCollectionViewのheaderの大きさを変更可能にする
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, referenceSizeForHeaderInSection section: Int) -> CGSize {
        return .init(width: collectionView.frame.width, height: 10)
    }
    
    // cellの大きさを決める
    // CGSize -> 幅と高さの情報を保持する
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        // CGFloat -> 座標や画像のサイズなどに使う型
        var width: CGFloat = 0
        // cellの横幅 = (画面の横幅 - 隙間の数 * 隙間の大きさ) / 4
        // frame -> 要素自身を基準とした相対的な座標・大きさ
        width = ((collectionView.frame.width - 10) - 14 * 5) / 4
        let height = width
        if indexPath.section == 4 , indexPath.row == 0 {
            width = width * 2 + 14 + 9
        }
        return.init(width: width, height: height)
    }
    
    // 隙間を調整
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, minimumInteritemSpacingForSectionAt section: Int) -> CGFloat {
        return 14
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        // dequeueReusableCell -> cellの再利用
        // as! -> cellの型をCalculatorViewCell型にする
        let cell = calculatorCollectionView.dequeueReusableCell(withReuseIdentifier: "cellIdentifier", for: indexPath) as! CalculatorViewCell
        // 配列(numbers)の数字を表示
        cell.numberLabel.text = numbers[indexPath.section][indexPath.row]
        
        numbers[indexPath.section][indexPath.row].forEach { (numberString) in
            if "0"..."9" ~= numberString || numberString.description == "." {
                cell.numberLabel.backgroundColor = .darkGray
            } else if numberString == "C" || numberString == "%" || numberString == "$" {
                cell.numberLabel.backgroundColor = UIColor.init(white: 1, alpha: 0.7)
                cell.numberLabel.textColor = .black
            }
        }
        return cell
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        let number = numbers[indexPath.section][indexPath.row]
        
        switch calculateStatus {
        case .none:
            switch number {
            case "0"..."9":
                firstNumber += number
                numberLabel.text = firstNumber
                
                if firstNumber.hasPrefix("0") { // hasPrefix -> 文字列の先頭が一致しているかどうか
                    firstNumber = ""
                }
            case ".":
                if !confirmIncludeDecimalPoint(numberString: firstNumber) {
                    firstNumber += number
                    numberLabel.text = firstNumber
                }
            case "+":
                calculateStatus = .plus
            case "-":
                calculateStatus = .minus
            case "×":
                calculateStatus = .multiplication
            case "÷":
                calculateStatus = .division
            case "C":
                clear()
            default:
                break
            }
        case .plus, .minus, .multiplication, .division:
            switch number {
            case "0"..."9":
                secondNumber += number
                numberLabel.text = secondNumber
                
                if secondNumber.hasPrefix("0") { // hasPrefix -> 文字列の先頭が一致しているかどうか
                    secondNumber = ""
                }
            case ".":
                if !confirmIncludeDecimalPoint(numberString: secondNumber) {
                    secondNumber += number
                    numberLabel.text = secondNumber
                }
            case "=":
                let firstNum = Double(firstNumber) ?? 0
                let secondNum = Double(secondNumber) ?? 0
                
                var resultString: String?
                switch calculateStatus {
                case .plus:
                    resultString = String(firstNum + secondNum)
                case .minus:
                    resultString = String(firstNum - secondNum)
                case .multiplication:
                    resultString = String(firstNum * secondNum)
                case .division:
                    resultString = String(firstNum / secondNum)
                default :
                    break
                }
                
                if let result = resultString, result.hasSuffix(".0") { // hasSuffix -> 文字列の最後尾が一致しているか
                    resultString = result.replacingOccurrences(of: ".0", with: "")
                }
                
                numberLabel.text = resultString
                
                firstNumber = ""
                secondNumber = ""
                
                firstNumber += resultString ?? ""
                calculateStatus = .none
                
            case "C":
                clear()
            default:
                break
            }
        }
    }
    
    // 文字列に.があるか、文字列が空のときtrue
    private func confirmIncludeDecimalPoint(numberString: String) -> Bool {
        if numberString.range(of: ".") != nil || numberString.count == 0 {
            return true
        } else {
            return false
        }
    }
}

class CalculatorViewCell: UICollectionViewCell {
    
    override var isHighlighted: Bool {
        // didSet -> 変数宣言or変更後に実行
        didSet {
            if isHighlighted {
                self.numberLabel.alpha = 0.3
            } else {
                self.numberLabel.alpha = 1
            }
        }
    }
    
    let numberLabel: UILabel = {
        let label = UILabel()
        label.textColor = .white
        label.textAlignment = .center
        label.text = "1"
        label.font = .boldSystemFont(ofSize: 32)
        label.clipsToBounds = true
        label.backgroundColor = .orange
        return label
    }()
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        addSubview(numberLabel)
        
        // cellと同じ大きさにする
        numberLabel.frame.size = self.frame.size
        // cellの角を削る
        numberLabel.layer.cornerRadius = self.frame.height / 2
    }
    
    // 
    required init?(coder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
}
