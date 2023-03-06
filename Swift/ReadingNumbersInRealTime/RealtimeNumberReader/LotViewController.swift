//
//  LotViewController.swift
//  RealtimeNumberReader
//
//  Created by Tanaka Lab on R 4/06/13.
//  Copyright © Reiwa 4 Apple. All rights reserved.
//

import UIKit

class LotViewController: UIViewController {
    
    @IBOutlet weak var label: UILabel! {
        didSet {
            label.text = "date: \(dateString) serial: \(serialString)"
        }
    }
    
    var dateString = ""
    var serialString = ""
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
}
