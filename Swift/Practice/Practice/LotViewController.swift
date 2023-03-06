//
//  LotViewController.swift
//  Practice
//
//  Created by Tanaka Lab on R 4/04/29.
//

import UIKit

class LotViewController: UIViewController {
    
    @IBOutlet weak var lotLabel: UILabel!
    @IBOutlet weak var lotTopImageView: UIImageView!
    @IBOutlet weak var startButton: UIButton!
    @IBOutlet weak var topView: UIView!
    @IBOutlet weak var lotImageView: UIImageView!
    
    var lot = ["大吉","吉","中吉","小吉","末吉","凶","大凶"]
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        topView.backgroundColor = .clear
        lotLabel.backgroundColor = .clear
    }
    
    
    @IBAction func startButton(_ sender: UIButton) {
        
        let randomLot = lot.randomElement()
        
        // top画面のものを消す
        startButton.alpha = 0.0
        lotLabel.alpha = 0.0
//        startButton.isEnabled = false
        lotTopImageView.isHidden = true
        
        // 逆さおみくじ出現
        lotImageView.alpha = 1.0
        // アニメーション部分
        // 逆再生でもwithduration秒使ってる? 動かす分逆方向に動いてる状態から始まってしまう. とりあえず動きはうまくいった
        UIView.animate(withDuration: 0.5, delay: 0.5, options: .autoreverse, animations: {
            self.lotImageView.center.y += 100.0
        }) { _ in
            self.lotImageView.center.y -= 100.0
        }
        UIView.animate(withDuration: 0.5, delay: 1.5, options: .autoreverse, animations: {
            self.lotImageView.center.y += 100.0
        }) { _ in
            self.lotImageView.center.y -= 100.0
        }
        // curveEaseIn -> 動き始めがゆっくりになる
        UIView.animate(withDuration: 0.5, delay: 2.5, options: [.curveEaseIn], animations: {
            self.lotImageView.alpha = 0.0
        }, completion: nil)
        
        lotLabel.text = randomLot
        // おみくじ結果出現
        UIView.animate(withDuration: 0.5, delay: 3.0, options: [.curveEaseIn], animations: {
            self.lotLabel.alpha = 1.0
            self.startButton.alpha = 1.0
        }, completion: nil)
    }
    
    // duration: アニメーション時間 delay: 開始までの遅延時間 options: アニメーションがどういうふうに動くか(動く速度、もとに戻すなど) animations: アニメーションしたいUIViewクラスのプロパティの値を変更 completion: アニメーションが完了したタイミングで呼ばれる
    open class func animate(withDuration duration: TimeInterval, delay: TimeInterval, options: UIView.AnimationOptions = [], animations: @escaping () -> Swift.Void, completion: ((Bool) -> Swift.Void)? = nil) {
    
    }
}
