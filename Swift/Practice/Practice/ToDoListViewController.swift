//
//  ToDoListController.swift
//  Practice
//
//  Created by Tanaka Lab on R 4/05/04.
//

import UIKit

class ToDoListViewController: UIViewController, UITableViewDelegate, UITableViewDataSource {
    
    @IBOutlet weak var tableView: UITableView!
    
    var toDoList = [String]()
    
    // 保存するインターフェイスを使用可能にする
    let userDefaults = UserDefaults.standard
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        // データ読み込み
        if let storedToDoList = userDefaults.array(forKey: "toDoList") as? [String] {
            toDoList.append(contentsOf: storedToDoList)
        }
    }

    @IBAction func addButton(_ sender: UIBarButtonItem) {
        let alertController = UIAlertController(title: "ToDo追加", message: "ToDoを入力してください。", preferredStyle: UIAlertController.Style.alert)
        alertController.addTextField(configurationHandler: nil)
        let okAction = UIAlertAction(title: "OK", style: UIAlertAction.Style.default) { (acrion: UIAlertAction) in // ?
            if let textField = alertController.textFields?.first {
                self.toDoList.insert(textField.text!, at: 0)
                self.tableView.insertRows(at: [IndexPath(row: 0, section: 0)], with: UITableView.RowAnimation.right)
                // キーボードを表示する
                textField.becomeFirstResponder()
                // 追加したToDoを保存
                self.userDefaults.set(self.toDoList, forKey: "toDoList")
            }
        }
        alertController.addAction(okAction)
        let cancelButton = UIAlertAction(title: "CANCEL", style: UIAlertAction.Style.cancel, handler: nil)
        alertController.addAction(cancelButton)
        present(alertController, animated: true, completion: nil)
    }
    
    // cellの数を指定
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return toDoList.count
    }
    
    // cellの中身を設定
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ToDoCell", for: indexPath)
        let toDoTitle = toDoList[indexPath.row]
        cell.textLabel?.text = toDoTitle
        return cell
    }
    
    // cellの削除機能
    func tableView(_ tableView: UITableView, commit editingStyle: UITableViewCell.EditingStyle, forRowAt indexPath:IndexPath) {
        if editingStyle == UITableViewCell.EditingStyle.delete {
//            確認用
//            print(toDoList)
            toDoList.remove(at: indexPath.row)
            tableView.deleteRows(at: [indexPath as IndexPath], with: UITableView.RowAnimation.automatic)
            // 削除した内容を保存
            userDefaults.set(toDoList, forKey: "toDoList")
        }
    }
}
