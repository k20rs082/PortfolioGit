//
//  PlusMinusViewController.swift
//  Practice
//
//  Created by Tanaka Lab on 2022/04/27.
//

import UIKit

class PlusMinusViewController: UIViewController {
    
    var count = 0
    
    @IBOutlet weak var countLabel: UILabel!
    //IBOutlet=値を入れたり画面に表示するもの
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }

    //IBAction=パーツが押されたときに呼び出されるメソッド
    @IBAction func countUpButton(_ sender: UIButton) {
        count += 1
        countLabel.text = String(count)
    }
    
    @IBAction func countDownButton(_ sender: UIButton) {
        count -= 1
        countLabel.text = String(count)
    }
    
}
