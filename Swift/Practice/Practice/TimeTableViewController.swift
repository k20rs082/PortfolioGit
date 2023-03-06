//
//  TimeTableViewController.swift
//  Practice
//
//  Created by Tanaka Lab on R 4/05/06.
//

import UIKit

class TimeTableViewController: UIViewController, UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout {
    
    @IBOutlet weak var timeTableCollectionView: UICollectionView!
    
    let numbers = [
        [" ", "月", "火", "水", "木", "金"],
        ["1", " ", " ", "ハードウェア設計Ⅲ", "データベース", " "],
        ["2", "オブジェクト指向設計", "データ構造とアルゴリズムⅡ", " ", "知能情報システム論", "ヒューマンコンピュータインタラクション"],
        ["3", "プログラミング入門(SA)", " ", "情報科学演習Ⅰ", " ", "ハードウェア実験Ⅱ"],
        ["4", "実践キャリア演習B", "インターンシップ", "Webプログラミング演習", " ", "ハードウェア実験Ⅱ"],
        ["5", " ", " ", " ", " ", " "]
    ]
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        
        timeTableCollectionView.register(TimeTableViewCell.self, forCellWithReuseIdentifier: "cellId")
        // ボタン配置部分を透明にして背景を映す
        timeTableCollectionView.backgroundColor = .clear
        
        timeTableCollectionView.contentInset = .init(top: 3, left: 5, bottom: 16, right: 5)
        view.backgroundColor = .black
    }
    
    // numbersの数だけラベルを表示
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        return numbers.count
    }
    
    // cellの数を返す
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
        var height: CGFloat = 0
        // cellの横幅 = (画面の横幅 - 隙間の横幅 * 隙間の数) / 6
        // frame -> 要素自身を基準とした相対的な座標・大きさ
        width = ((collectionView.frame.width) - 5 * 7) / 6
        height = ((collectionView.frame.height - 16) - 3 * 6) / 6
        if indexPath.row == 0 {
            width = width / 1.5
        }
        if indexPath.section == 0 {
            height = height / 4
        }
        return.init(width: width, height: height)
    }
    
    // 隙間を調整
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, minimumInteritemSpacingForSectionAt section: Int) -> CGFloat {
        return 5
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        // dequeueReusableCell -> cellの再利用
        // as! -> cellの型をTimeTableViewCell型にする
        let cell = timeTableCollectionView.dequeueReusableCell(withReuseIdentifier: "cellId", for: indexPath) as! TimeTableViewCell
        // 配列(numbers)の数字を表示
        cell.numberLabel.text = numbers[indexPath.section][indexPath.row]
        
        cell.numberLabel.numberOfLines = 0
        
        numbers[indexPath.section][indexPath.row].forEach { (numberString) in
            if cell.numberLabel.text == " " || indexPath.section == 0 || indexPath.row == 0 {
                cell.numberLabel.backgroundColor = .darkGray
                cell.numberLabel.textColor = .white
            } else {
                cell.numberLabel.textColor = UIColor.init(white: 1, alpha: 0.9)
            }
        }
        
        return cell
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        // キーボードを出す
        let cell = collectionView.cellForItem(at: indexPath)
        
    }
    
    
}

class TimeTableViewCell: UICollectionViewCell {
    
    let numberLabel: UILabel = {
        let label = UILabel()
        label.textColor = .white
        label.textAlignment = .center
        label.text = "1"
        label.font = .boldSystemFont(ofSize: 16)
        label.clipsToBounds = true
        label.backgroundColor = .systemTeal
        return label
    }()
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        addSubview(numberLabel)
        
        // cellと同じ大きさにする
        numberLabel.frame.size = self.frame.size
    }

    required init?(coder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
}

