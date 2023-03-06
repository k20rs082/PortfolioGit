//
//  TimeTableViewController.swift
//  Practice
//
//  Created by Tanaka Lab on R 4/05/06.
//

import UIKit

class SecondTimeTableViewController: UIViewController, UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout, UITextViewDelegate {
    
    @IBOutlet weak var timeTableCollectionView: UICollectionView!
    @IBOutlet weak var timeTableCollectionViewFlowLayout: UICollectionViewFlowLayout!
    
    let numbers = [
        " ", "月", "火", "水", "木", "金"
    ]
    
    var textViewTag = 0
//    var timeTableList = [[String]](repeating: [String](repeating: "", count: 5), count:5)
    var timeTableList = [[String]]()

    // これなしでUserDefaults.standardで書いてもいい
    let userDefaults = UserDefaults.standard
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        
        // userDefaultsの初期値設定
        userDefaults.register(defaults: ["timeTableList" : [[String]](repeating: [String](repeating: "", count: 5), count:5)])
//         データ読み込み
        if let storedTimeTableList = userDefaults.array(forKey: "timeTableList") as? [[String]] {
            timeTableList = storedTimeTableList
        }
        
        //
        timeTableCollectionView.register(UINib(nibName: "TimeTableCell", bundle: nil), forCellWithReuseIdentifier: "cell")
        
        // ボタン配置部分を透明にして背景を映す
        timeTableCollectionView.backgroundColor = .clear
        
        timeTableCollectionView.contentInset = .init(top: 4, left: 8, bottom: 16, right: 16)
        view.backgroundColor = .black
        
        // 画面のどこかをタップしたらdismissKeyboard()を呼び出す
        let tapGR: UITapGestureRecognizer = UITapGestureRecognizer(target: self, action: #selector(dismissKeyboard))
        // 他のタップ関連のイベントが実行されるようにする。
        tapGR.cancelsTouchesInView = false
        // viewにUITapGestureRecognizerを追加
        self.view.addGestureRecognizer(tapGR)

    }
    
    // viewのendEditing()メソッドを実行
    @objc func dismissKeyboard() {
        self.view.endEditing(true)
    }
    
    // cellが何行か指定する
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        return 6
    }
    
    // １行のcellの数を指定する
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return 6
    }

    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {

        var width = (timeTableCollectionView.frame.width - 4 * 11) / 6
        var height = (timeTableCollectionView.frame.height - 4 * 10 ) / 6
       

        if indexPath.section == 0 {
            height = height / 4
        }
        if indexPath.row == 0 {
            width = width / 1.5
        }

        return CGSize(width: width, height: height)
    }
    
    // cellの中身を指定
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        
        if let cell = timeTableCollectionView.dequeueReusableCell(withReuseIdentifier: "cell", for: indexPath) as? TimeTableCell {
            
            let layout = UICollectionViewFlowLayout()
            layout.minimumInteritemSpacing = 4
            layout.minimumLineSpacing = 4
            timeTableCollectionView.collectionViewLayout = layout
            
            cell.timeTableLabel.textAlignment = .center
            cell.timeTableLabel.textColor = .white
            cell.textView.backgroundColor = .darkGray
            cell.textView.textColor = .white

            if indexPath.section == 0 {
                cell.timeTableLabel.text = numbers[indexPath.row]
                // isEnabled -> 有効かどうか指定、falseだから無効
                cell.textView.isEditable = false
            } else if indexPath.row == 0{
                cell.timeTableLabel.text = "\(indexPath.section)"
                cell.textView.isEditable = false
            } else {
                cell.textView.text = timeTableList[indexPath.section - 1][indexPath.row - 1]
                // isHidden -> 非表示にする
                cell.timeTableLabel.isHidden = true
            }
            
            cell.textView.delegate = self // 保存機能追加分
            cell.textView.tag = indexPath.section*16+indexPath.row
            cell.textView.addGestureRecognizer(UITapGestureRecognizer(target: self, action: #selector(self.textViewTapped(_:))))
            
            return cell
        }
        return UICollectionViewCell()
    }
    
    @objc private func textViewTapped(_ sender: UITapGestureRecognizer) {
        // キーボードを出す
        sender.view?.becomeFirstResponder()
        
        textViewTag = sender.view?.tag ?? 0
        
        // どのcellを押したかチェック用
        // print("section: \((sender.view?.tag ?? 0)/16) row: \((sender.view?.tag ?? 0)%16)")
    }
    
    // textViewの入力終わったとき
    func textViewDidEndEditing(_ textView: UITextView) {
        // guard -> 条件を満たさない場合に終了する
        guard let text = textView.text else { return }
        
        print(text)
        timeTableList[(textViewTag/16)-1][(textViewTag%16)-1] = text
        print(timeTableList)
        self.userDefaults.set(self.timeTableList, forKey: "timeTableList")
    } // 保存機能追加分
}
